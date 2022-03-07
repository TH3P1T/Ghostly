<?php

namespace Ghostly\Handlers;

class NotFound extends \Slim\Handlers\NotFound {
    protected $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public function __invoke($request, $response) {
        // Log request
        $path = $request->getUri()->getPath();
        //$this->logger->info("Not Found (404): {$request->getMethod()} {$path}");

        if (preg_match('/^.*\.(jpg|jpeg|png|gif)$/i', $path)) {
            return $response->withStatus(404);
        }

        return parent::__invoke($request, $response);
    }

    protected function renderHtmlNotFoundOutput($request)
    {
        $response = new \Slim\Http\Response(404);

        return $this->container->get('renderer')->render($response, "/errors/404.php")->getBody();
    }
}