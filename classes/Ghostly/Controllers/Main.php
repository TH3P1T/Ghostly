<?php

namespace Ghostly\Controllers;

class Main extends \Ghostly\Controller
{
    public function home($request, $response, $args) {
        if ($request->getAttribute('authenticated') === TRUE) {
            return $response->withRedirect($this->container->get('router')->pathFor('dashboard'));
        }
        return $response->withRedirect($this->container->get('router')->pathFor('login'));
    }

    public function login($request, $response, $args) {
        $this->template_variables['login_failed'] = FALSE;
        $this->fetchCsrfVariables($request);

        return $this->container->get('renderer')->render($response, "/login.php", $this->template_variables);
    }
    
    public function process_login($request, $response, $args) {
        $authentication_middleware = $this->container->get('middleware')['authentication'];
        $authentication_service = $this->container->get('services')['authentication'];
        $users_service = $this->container->get('services')['users'];

        $username = $request->getParam('username');
        $password = $request->getParam('password');

        if ($authentication_service->verifyUserAndPassword($username, $password)) {
            $authentication_middleware->setAuthenticatedUserId($authentication_service->getUserIdFromUser($username));
            $redirect_target = $authentication_middleware->getRedirectAndReset();
            
            $_SESSION['user_timezone'] = "";
            $current_user = $users_service->getById($authentication_middleware->getAuthenticatedUserId());
            if ($current_user != false) {
                $_SESSION['user_timezone'] = $current_user->timezone;
            }

            if ($redirect_target !== FALSE) {
                return $response->withRedirect($redirect_target);
            }

            return $response->withRedirect($this->container->get('router')->pathFor('home'));
        }

        $this->template_variables['login_failed'] = TRUE;
        $this->fetchCsrfVariables($request);

        return $this->container->get('renderer')->render($response, "/login.php", $this->template_variables);
    }

    public function logout($request, $response, $args) {
        $authentication_middleware = $this->container->get('middleware')['authentication'];
        $authentication_middleware->unsetAuthenticatedUserId();

        return $response->withRedirect($this->container->get('router')->pathFor('home'));
    }

    public function dashboard($request, $response, $args) {
        $this->template_variables['login_failed'] = FALSE;
        $this->fetchCsrfVariables($request);

        $licenses_service = $this->container->get('services')['licenses'];
        $products_service = $this->container->get('services')['products'];
        $authorizations_service = $this->container->get('services')['authorizations'];

        $this->template_variables['licenses_total'] = $licenses_service->total();
        $this->template_variables['products_total'] = $products_service->total();
        $this->template_variables['authorizations_valid_total'] = $authorizations_service->totalValid();
        $this->template_variables['authorizations_invalid_total'] = $authorizations_service->totalInvalid();

        $this->template_variables['latest_products'] = $products_service->latestSummary(10);
        $this->template_variables['latest_licenses'] = $licenses_service->latestSummary(10);
        $this->template_variables['latest_authorizations_valid'] = $authorizations_service->latestSummaryValid(10);
        $this->template_variables['latest_authorizations_invalid'] = $authorizations_service->latestSummaryInvalid(10);

        foreach ($this->template_variables['latest_authorizations_valid'] as $key => $authorization) {
            $created_date = new \DateTime($authorization['created'], new \DateTimeZone("UTC"));
            $utc_now = new \DateTime("now", new \DateTimeZone("UTC"));

            $diff_seconds = $utc_now->getTimestamp() - $created_date->getTimestamp();

            $this->template_variables['latest_authorizations_valid'][$key]['when'] = $this->whenString($diff_seconds);
        }

        foreach ($this->template_variables['latest_authorizations_invalid'] as $key => $authorization) {
            $created_date = new \DateTime($authorization['created'], new \DateTimeZone("UTC"));
            $utc_now = new \DateTime("now", new \DateTimeZone("UTC"));

            $diff_seconds = $utc_now->getTimestamp() - $created_date->getTimestamp();

            $this->template_variables['latest_authorizations_invalid'][$key]['when'] = $this->whenString($diff_seconds);
        }

        return $this->container->get('renderer')->render($response, "/dashboard.php", $this->template_variables);
    }

    private function whenString($since) {
        $chunks = array(
            array(60 * 60 * 24 * 365 , 'year'),
            array(60 * 60 * 24 * 30 , 'month'),
            array(60 * 60 * 24 * 7, 'week'),
            array(60 * 60 * 24 , 'day'),
            array(60 * 60 , 'hour'),
            array(60 , 'minute'),
            array(1 , 'second')
        );
    
        for ($i = 0, $j = count($chunks); $i < $j; $i++) {
            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];
            if (($count = floor($since / $seconds)) != 0) {
                break;
            }
        }
    
        $print = ($count == 1) ? '1 '.$name : "$count {$name}s";
        return $print;
    }
}