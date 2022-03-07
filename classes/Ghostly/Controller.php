<?php

namespace Ghostly;

use Psr\Container\ContainerInterface;

class Controller {
    protected $container;
    protected $template_variables = array();

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function fetchCsrfVariables($request) {
        $csrf_middleware = $this->container->get('middleware')['csrf'];
        
        $nameKey = $csrf_middleware->getTokenNameKey();
        $valueKey = $csrf_middleware->getTokenValueKey();
        $name = $request->getAttribute($nameKey);
        $value = $request->getAttribute($valueKey);

        $this->template_variables['csrf_name_key'] = $nameKey;
        $this->template_variables['csrf_value_key'] = $valueKey;
        $this->template_variables['csrf_name'] = $name;
        $this->template_variables['csrf_value'] = $value;
    }
}