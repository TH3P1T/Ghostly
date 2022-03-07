<?php

namespace Ghostly\Controllers;

class Install extends \Ghostly\Controller
{
    public function install($request, $response, $args) {
        $this->fetchCsrfVariables($request);

        $this->template_variables['php_version_ok'] = (PHP_MAJOR_VERSION > 5 ? true : false);
        $this->template_variables['initialize_db'] = $this->databaseInitialized();
        $this->template_variables['setup_root_user'] = FALSE;

        if ($this->template_variables['initialize_db'] == TRUE) {
            $this->template_variables['setup_root_user'] = $this->rootUserExists();
        }

        $this->template_variables['extension_gmp'] = extension_loaded('gmp');
        $this->template_variables['extension_bcmath'] = extension_loaded('bcmath');
        $this->template_variables['extension_openssl'] = extension_loaded('openssl');

        return $this->container->get('renderer')->render($response, "/install.php", $this->template_variables);
    }

    public function process_install($request, $response, $args) {
        $this->fetchCsrfVariables($request);

        $this->template_variables['php_version_ok'] = (PHP_MAJOR_VERSION > 5 ? true : false);
        $this->template_variables['initialize_db'] = $this->initializeDatabase();
        $this->template_variables['setup_root_user'] = $this->setupRootUser();
        $this->template_variables['extension_gmp'] = extension_loaded('gmp');
        $this->template_variables['extension_bcmath'] = extension_loaded('bcmath');
        $this->template_variables['extension_openssl'] = extension_loaded('openssl');

        return $this->container->get('renderer')->render($response, "/install.php", $this->template_variables);
    }

    protected function setupRootUser() {
        try {
            $user = \ORM::forTable('users')->where('username', 'root')->findOne();
            if ($user !== FALSE) {
                return TRUE;
            }

            $user = \ORM::forTable('users')->create();
            $user->username = 'root';
            $user->password = password_hash('weakpassword', PASSWORD_DEFAULT);
            $user->role = 'ADMIN';
            $user->set_expr('created', 'UTC_TIMESTAMP()');
            $user->set_expr('updated', 'UTC_TIMESTAMP()');

            return $user->save();
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    protected function initializeDatabase() {
        if ($this->databaseInitialized()) {
            return TRUE;
        }

        try {
            $created_authorizations = FALSE;
            $created_endpoints = FALSE;
            $created_licenses = FALSE;
            $created_products = FALSE;
            $created_users = FALSE;

            if (\ORM::raw_execute('CREATE TABLE `authorizations` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `license_key` varchar(255) NOT NULL,
                `hardware_id` varchar(255) DEFAULT NULL,
                `ip_address` varchar(255) DEFAULT NULL,
                `created` datetime NOT NULL,
                `response` text,
                `success` tinyint(4) NOT NULL DEFAULT \'0\',
                PRIMARY KEY (`id`)
              ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;')) 
            {
                $created_authorizations = TRUE;
            } else {
                $created_authorizations = FALSE;
            }

            if (\ORM::raw_execute('CREATE TABLE `endpoints` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `key` varchar(255) NOT NULL,
                `secret` varchar(255) NOT NULL,
                `created` datetime NOT NULL,
                `updated` datetime NOT NULL,
                PRIMARY KEY (`id`)
              ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;')) 
            {
                $created_endpoints = TRUE;
            } else {
                $created_endpoints = FALSE;
            }

            if (\ORM::raw_execute('CREATE TABLE `products` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `short_name` varchar(255) NOT NULL,
                `description` varchar(255) DEFAULT NULL,
                `created` datetime NOT NULL,
                `updated` datetime DEFAULT NULL,
                `private_key` text NOT NULL,
                `public_key` text NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `products_name` (`name`),
                UNIQUE KEY `products_short_name` (`short_name`)
              ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;')) 
            {
                $created_products = TRUE;
            } else {
                $created_products = FALSE;
            }

            if (\ORM::raw_execute('CREATE TABLE `licenses` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `product_id` int(10) unsigned NOT NULL,
                `key` varchar(255) NOT NULL,
                `customer_name` varchar(255) DEFAULT NULL,
                `customer_email` varchar(255) DEFAULT NULL,
                `purchase_date` datetime NOT NULL,
                `order_reference` varchar(255) DEFAULT NULL,
                `comments` text,
                `hardware_id` varchar(255) DEFAULT NULL,
                `expiration_date` datetime DEFAULT NULL,
                `disabled` tinyint(4) NOT NULL DEFAULT \'0\',
                `created` datetime NOT NULL,
                `updated` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `license_key_unique` (`key`),
                KEY `license_product_id` (`product_id`),
                CONSTRAINT `license_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE
              ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;')) 
            {
                $created_licenses = TRUE;
            } else {
                $created_licenses = FALSE;
            }

            if (\ORM::raw_execute('CREATE TABLE `users` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `username` varchar(255) NOT NULL,
                `password` varchar(255) NOT NULL,
                `created` datetime NOT NULL,
                `updated` datetime NOT NULL,
                `role` enum(\'ADMIN\',\'USER\') NOT NULL DEFAULT \'USER\',
                `notes` text,
                `timezone` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `users_username` (`username`)
              ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;
              SET FOREIGN_KEY_CHECKS=1;')) 
            {
                $created_users = TRUE;
            } else {
                $created_users = FALSE;
            }

            if ($created_authorizations == TRUE && $created_endpoints == TRUE && $created_licenses &&
                $created_products == TRUE && $created_users == TRUE)
            {
                return TRUE;
            }
        }
        catch( \PDOException $exception) {
            return FALSE;
        }

        return FALSE;
    }

    protected function databaseInitialized() {
        $authorizations_exists = $this->sqlTableExists('authorizations');
        $endpoints_exists = $this->sqlTableExists('endpoints');
        $licenses_exists = $this->sqlTableExists('licenses');
        $products_exists = $this->sqlTableExists('products');
        $users_exists = $this->sqlTableExists('users');

        if ($authorizations_exists == TRUE && $endpoints_exists == TRUE && $licenses_exists == TRUE &&
            $products_exists == TRUE && $users_exists == TRUE) 
        {
            return TRUE;
        }

        return FALSE;
    }

    protected function rootUserExists() {
        try {
            $root_user = \ORM::forTable('users')->where_like('username', 'root')->findArray();
            if ($root_user !== FALSE && count($root_user) > 0) {
                return TRUE;
            }
        }
        catch( \PDOException $Exception ) {
            return false;
        }

        return FALSE;
    }

    protected function sqlTableExists($table_name) {
        if (strlen($table_name) == 0) {
            return FALSE;
        }

        $result = \ORM::raw_execute('SHOW TABLES LIKE \'' . $table_name . '\';');
        $statement = \ORM::get_last_statement();
        if ($statement == FALSE) {
            return FALSE;
        }

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            foreach ($row as $key => $value) {
                if ($value == $table_name) {
                    return TRUE;
                }
            }
        }

        return FALSE;
    }
}