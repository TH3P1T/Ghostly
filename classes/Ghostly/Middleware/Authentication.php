<?php

namespace Ghostly\Middleware;

class Authentication {
    private $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public function __invoke($request, $response, $next) {
        if ($this->hasAuthenticatedUserId()) {
            $redirect_target = $this->getRedirectAndReset();
            $request = $request->withAttribute('user_id', $this->getAuthenticatedUserId());
            $request = $request->withAttribute('authenticated', TRUE);

            if ($redirect_target !== FALSE) {
                return $next($request, $response)->withRedirect($redirect_target);
            }

            return $next($request, $response);
        }
        
        $request = $request->withAttribute('authenticated', FALSE);
        $this->setRedirect($request->getRequestTarget());

        return $response->withRedirect($this->container->get('router')->pathFor('login'));
    }

    public function setRedirect($location) {
        $_SESSION['redirect_target'] = $location;
    }

    public function getRedirectAndReset() {
        if (!array_key_exists('redirect_target', $_SESSION)) {
            return FALSE;
        }

        $location = $_SESSION['redirect_target'];
        unset($_SESSION['redirect_target']);

        return $location;
    }

    public function hasRedirect() {
        return array_key_exists('redirect_target', $_SESSION);
    }

    public function setAuthenticatedUserId($user_id) {
        $_SESSION['user_id'] = $user_id;
    }

    public function getAuthenticatedUserId() {
        return $_SESSION['user_id'];
    }

    public function unsetAuthenticatedUserId() {
        if (array_key_exists('user_id', $_SESSION)) {
            unset($_SESSION['user_id']);
        }
    }

    public function hasAuthenticatedUserId() {
        return array_key_exists('user_id', $_SESSION);
    }
}