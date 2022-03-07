<?php

namespace Ghostly\Handlers;

class Error extends \Slim\Handlers\Error {
    protected $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public function __invoke($request, $response, $error) {
        //log it or something ...

        return parent::__invoke($request, $response, $error);
    }

    protected function renderHtmlErrorMessage($error) {
        $response = new \Slim\Http\Response(500);

        return $this->container->get('renderer')->render($response, "/errors/500.php")->getBody();
    }
}