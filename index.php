<?php

//setup environment autoload, session init, site config
define("GHOSTLY_INTERNAL", TRUE);
define("AUTOLOAD_CLASS_PATH", __DIR__ . DIRECTORY_SEPARATOR . "classes");

require AUTOLOAD_CLASS_PATH . DIRECTORY_SEPARATOR . "Ghostly" . DIRECTORY_SEPARATOR . "ClassLoader.php";

$loader = new Ghostly\ClassLoader();

$loader->addPrefix(NULL, AUTOLOAD_CLASS_PATH);
$loader->register();

require AUTOLOAD_CLASS_PATH . DIRECTORY_SEPARATOR . "RandomCompat" . DIRECTORY_SEPARATOR . "random.php";
require AUTOLOAD_CLASS_PATH . DIRECTORY_SEPARATOR . "FastRoute" . DIRECTORY_SEPARATOR . "functions.php";
require AUTOLOAD_CLASS_PATH . DIRECTORY_SEPARATOR . "Idiorm" . DIRECTORY_SEPARATOR . "idiorm.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//timeout sessions after 30 minutes
if (isset($_SESSION['last_site_activity']) && (time() - $_SESSION['last_site_activity'] > 1800)) {
    session_unset();
    session_destroy();
}
$_SESSION['last_site_activity'] = time();

//regenerate session id every 30 minutes of activity
if (!isset($_SESSION['session_created'])) {
    $_SESSION['session_created'] = time();
} else if (time() - $_SESSION['session_created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['session_created'] = time();
}

require "config.php";

//setup dependencies
ORM::configure($site_config['db_connection']);
ORM::configure('username', $site_config['db_username']);
ORM::configure('password', $site_config['db_password']);

$container = new \Slim\Container(array('settings' => $site_config['framework']));

$global_template_variables = [
    'url_base' => $site_config['url_base'],
    'install_enabled' => $site_config['install_enabled']
];
$container['renderer'] = new \Slim\Views\PhpRenderer("./templates", $global_template_variables);

//error handlers
$container['notFoundHandler'] = function ($c) {
    return new Ghostly\Handlers\NotFound($c);
};

$container['notAllowedHandler'] = function ($c) {
    return new Ghostly\Handlers\NotAllowed($c);
};

$container['phpErrorHandler'] = function ($c) {
   return new Ghostly\Handlers\PhpError($c);
};

$container['errorHandler'] = function ($c) {
    return new Ghostly\Handlers\Error($c);
};

//middleware
$csrf_storage = null;
$middleware = [
    'csrf' => new \Slim\Csrf\Guard('csrf', $csrf_storage, null, 200, 16, true),
    'authentication' => new \Ghostly\Middleware\Authentication($container)
];

$container['middleware'] = $middleware;

//services
$services = [
    'authentication' => new Ghostly\Services\Authentication(),
    'users' => new Ghostly\Services\Users(),
    'products' => new Ghostly\Services\Products(),
    'licenses' => new Ghostly\Services\Licenses(),
    'authorizations' => new Ghostly\Services\Authorizations(),
    'endpoints' => new Ghostly\Services\Endpoints()
];

$container['services'] = $services;

//initialize app
$app = new \Slim\App($container);


//setup page routes
$app->group('', function (\Slim\App $app) {
    global $site_config, $container;

    if ($site_config['install_enabled']) {
        $app->get('/install', Ghostly\Controllers\Install::class . ':install')->setName('install');
        $app->post('/install', Ghostly\Controllers\Install::class . ':process_install');
        $app->post('/upgrade-1', Ghostly\Controllers\Install::class . ':process_upgrade_1');
        $app->post('/upgrade-2', Ghostly\Controllers\Install::class . ':process_upgrade_2');
        $app->post('/upgrade-3', Ghostly\Controllers\Install::class . ':process_upgrade_3');
    }

    $app->get('/login', Ghostly\Controllers\Main::class . ':login')->setName('login');
    $app->post('/login', Ghostly\Controllers\Main::class . ':process_login');
    $app->post('/logout', Ghostly\Controllers\Main::class . ':logout');
    
    $app->get('/hwid-reset', Ghostly\Controllers\Main::class . ':hwid_reset')->setName('hwid_reset');
    $app->post('/hwid-reset', Ghostly\Controllers\Main::class . ':process_hwid_reset');

    $app->group('', function (\Slim\App $app) {
        $app->get('/', Ghostly\Controllers\Main::class . ':home')->setName('home');    
        $app->get('/dashboard', Ghostly\Controllers\Main::class . ':dashboard')->setName('dashboard');

        //
        // PRODUCTS ROUTES
        //
        $app->get('/products', Ghostly\Controllers\Products::class . ':home')->setName('products_home');
        $app->get('/products/view/{id:[0-9]+}', Ghostly\Controllers\Products::class . ':view')->setName('products_view');
        $app->get('/products/add', Ghostly\Controllers\Products::class . ':add')->setName('products_add');
        $app->post('/products/add', Ghostly\Controllers\Products::class . ':process_add');

        $app->get('/products/edit/{id:[0-9]+}', Ghostly\Controllers\Products::class . ':edit')->setName('products_edit');
        $app->post('/products/edit/{id:[0-9]+}', Ghostly\Controllers\Products::class . ':process_edit');
        $app->post('/products/edit/{id:[0-9]+}/add-package-product', Ghostly\Controllers\Products::class . ':process_add_package_product');
        $app->post('/products/edit/{id:[0-9]+}/delete-package-product', Ghostly\Controllers\Products::class . ':process_delete_package_product');

        $app->get('/products/delete/{id:[0-9]+}', Ghostly\Controllers\Products::class . ':delete')->setName('products_delete');
        $app->post('/products/delete/{id:[0-9]+}', Ghostly\Controllers\Products::class . ':process_delete');
        
        $app->get('/products/ajax/list', Ghostly\Controllers\Products::class . ':ajax_list');
        
        //
        // LICENSES ROUTES
        //
        $app->get('/licenses', Ghostly\Controllers\Licenses::class . ':home')->setName('licenses_home');
        $app->get('/licenses/view/{id:[0-9]+}', Ghostly\Controllers\Licenses::class . ':view')->setName('licenses_view');
        
        $app->get('/licenses/add', Ghostly\Controllers\Licenses::class . ':add')->setName('licenses_add');
        $app->post('/licenses/add', Ghostly\Controllers\Licenses::class . ':process_add');
        $app->get('/licenses/add-bulk', Ghostly\Controllers\Licenses::class . ':add_bulk')->setName('licenses_add_bulk');
        $app->post('/licenses/add-bulk', Ghostly\Controllers\Licenses::class . ':process_add_bulk');

        $app->get('/licenses/edit/{id:[0-9]+}', Ghostly\Controllers\Licenses::class . ':edit')->setName('licenses_edit');
        $app->post('/licenses/edit/{id:[0-9]+}', Ghostly\Controllers\Licenses::class . ':process_edit');
        $app->post('/licenses/edit/reset-hwid/{id:[0-9]+}', Ghostly\Controllers\Licenses::class . ':process_reset_hardware_id');
        $app->post('/licenses/edit/add-1-day/{id:[0-9]+}', Ghostly\Controllers\Licenses::class . ':process_add_one_day');
        $app->post('/licenses/edit/add-1-month/{id:[0-9]+}', Ghostly\Controllers\Licenses::class . ':process_add_one_month');
        $app->post('/licenses/edit/add-1-year/{id:[0-9]+}', Ghostly\Controllers\Licenses::class . ':process_add_one_year');
        $app->post('/licenses/edit/add-3-months/{id:[0-9]+}', Ghostly\Controllers\Licenses::class . ':process_add_three_months');
        $app->post('/licenses/edit/disable-now/{id:[0-9]+}', Ghostly\Controllers\Licenses::class . ':process_disable_now');
        $app->post('/licenses/edit/enable-now/{id:[0-9]+}', Ghostly\Controllers\Licenses::class . ':process_enable_now');
        $app->post('/licenses/edit/clear-tags/{id:[0-9]+}', Ghostly\Controllers\Licenses::class . ':process_clear_tags');

        $app->get('/licenses/delete/{id:[0-9]+}', Ghostly\Controllers\Licenses::class . ':delete')->setName('licenses_delete');
        $app->post('/licenses/delete/{id:[0-9]+}', Ghostly\Controllers\Licenses::class . ':process_delete');

        $app->get('/licenses/ajax/list', Ghostly\Controllers\Licenses::class . ':ajax_list');
        $app->get('/licenses/add/tags/list', Ghostly\Controllers\Licenses::class . ':ajax_tags_list');
        $app->get('/licenses/add-bulk/tags/list', Ghostly\Controllers\Licenses::class . ':ajax_tags_list');
        $app->get('/licenses/edit/{id:[0-9]+}/tags/list', Ghostly\Controllers\Licenses::class . ':ajax_tags_list');
        $app->get('/licenses/view/{id:[0-9]+}/tags/list', Ghostly\Controllers\Licenses::class . ':ajax_tags_list');

        //
        // USERS ROUTES
        //
        $app->get('/users', Ghostly\Controllers\Users::class . ':home')->setName('users_home');
        $app->get('/users/view/{id:[0-9]+}', Ghostly\Controllers\Users::class . ':view')->setName('users_view');
        $app->get('/users/add', Ghostly\Controllers\Users::class . ':add')->setName('users_add');
        $app->post('/users/add', Ghostly\Controllers\Users::class . ':process_add');

        $app->get('/users/edit/{id:[0-9]+}', Ghostly\Controllers\Users::class . ':edit')->setName('users_edit');
        $app->post('/users/edit/{id:[0-9]+}', Ghostly\Controllers\Users::class . ':process_edit');

        $app->get('/users/delete/{id:[0-9]+}', Ghostly\Controllers\Users::class . ':delete')->setName('users_delete');
        $app->post('/users/delete/{id:[0-9]+}', Ghostly\Controllers\Users::class . ':process_delete');

        $app->get('/users/ajax/list', Ghostly\Controllers\Users::class . ':ajax_list');

        //
        // AUTHORIZATIONS ROUTES
        //
        $app->get('/authorizations', Ghostly\Controllers\Authorizations::class . ':home')->setName('authorizations_home');
        $app->get('/authorizations/view/{id:[0-9]+}', Ghostly\Controllers\Authorizations::class . ':view')->setName('authorizations_view');

        $app->get('/authorizations/ajax/list', Ghostly\Controllers\Authorizations::class . ':ajax_list');

        //
        // ENDPOINTS ROUTES
        //
        $app->get('/endpoints', Ghostly\Controllers\Endpoints::class . ':home')->setName('endpoints_home');
        $app->get('/endpoints/add', Ghostly\Controllers\Endpoints::class . ':add')->setName('endpoints_add');
        $app->post('/endpoints/add', Ghostly\Controllers\Endpoints::class . ':process_add');

        $app->get('/endpoints/edit/{id:[0-9]+}', Ghostly\Controllers\Endpoints::class . ':edit')->setName('endpoints_edit');
        $app->post('/endpoints/edit/{id:[0-9]+}', Ghostly\Controllers\Endpoints::class . ':process_edit');

        $app->get('/endpoints/delete/{id:[0-9]+}', Ghostly\Controllers\Endpoints::class . ':delete')->setName('endpoints_delete');
        $app->post('/endpoints/delete/{id:[0-9]+}', Ghostly\Controllers\Endpoints::class . ':process_delete');

        $app->get('/endpoints/ajax/list', Ghostly\Controllers\Endpoints::class . ':ajax_list');
        
    })->add($container->get('middleware')['authentication']);
})->add($container->get('middleware')['csrf']);

//setup api routes
$app->group('/api/v1/{endpoint}', function (\Slim\App $app) {
    $app->post('/authorize', Ghostly\Controllers\Api::class . ':authorize');
    
});

//run application
$app->run();
