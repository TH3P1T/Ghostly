<?php

namespace Ghostly\Handlers;

class NotAllowed extends \Slim\Handlers\NotAllowed {
    protected $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public function __invoke($request, $response, $methods) {
        //log it or something ...

        return parent::__invoke($request, $response, $methods);
    }

    protected function renderHtmlNotAllowedMessage($methods) {
        $response = new \Slim\Http\Response(405);

        return $this->container->get('renderer')->render($response, "/errors/405.php", array('allowed_methods' => implode(', ', $methods)))->getBody();
    }
}