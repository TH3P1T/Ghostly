<?php

namespace Ghostly\Controllers;

class Endpoints extends \Ghostly\Controller
{
    public function __construct($container) {
        parent::__construct($container);

        $this->template_variables['endpoint_id'] = "";
        $this->template_variables['endpoint_name'] = "";
        $this->template_variables['endpoint_key'] = "";
        $this->template_variables['endpoint_secret'] = "";
        $this->template_variables['add_attempted'] = false;
        $this->template_variables['added'] = false;
        $this->template_variables['edit_attempted'] = false;
        $this->template_variables['edited'] = false;
        $this->template_variables['delete_attempted'] = false;
        $this->template_variables['deleted'] = false;
        $this->template_variables['validation_failed'] = false;
    }

    public function home($request, $response, $args) {
        $this->fetchCsrfVariables($request);

        return $this->container->get('renderer')->render($response, "/endpoints/index.php", $this->template_variables);
    }

    public function ajax_list($request, $response, $args) {
        $endpoints_service = $this->container->get('services')['endpoints'];

        //current=1&rowCount=10&sort[sender]=asc&searchPhrase=
        $current = $request->getParam('current');
        $rowCount = $request->getParam('rowCount');
        $sort = $request->getParam('sort');
        $searchPhrase = $request->getParam('searchPhrase');

        $data = [
            'current' => $current,
            'rowCount' => $rowCount,
            'total' => 0,
            'rows' => []
        ];

        $data['total'] = $endpoints_service->total($searchPhrase);
        $data['rows'] = $endpoints_service->getPaged($current, $rowCount, $sort, $searchPhrase);

        foreach ($data['rows'] as $key => $value)
        {
            foreach ($value as $v_key => $v_value)
            {
                $value[$v_key] = htmlspecialchars($v_value);
            }

            $data['rows'][$key] = $value;
        }

        return $response->withJson($data);
    }

    public function add($request, $response, $args) {
        $endpoints_service = $this->container->get('services')['endpoints'];
        $this->fetchCsrfVariables($request);

        return $this->container->get('renderer')->render($response, "/endpoints/add.php", $this->template_variables);
    }

    public function process_add($request, $response, $args) {
        $endpoints_service = $this->container->get('services')['endpoints'];
        $this->fetchCsrfVariables($request);

        $endpoint_name = $request->getParam('name');
        $endpoint_key = $request->getParam('key');
        $endpoint_secret = $request->getParam('secret');

        $this->template_variables['endpoint_name'] = $endpoint_name;
        $this->template_variables['endpoint_key'] = $endpoint_key;
        $this->template_variables['endpoint_secret'] = $endpoint_secret;
        $this->template_variables['add_attempted'] = true;

        //validate

        $this->template_variables['added'] = $endpoints_service->add($endpoint_name, $endpoint_key, $endpoint_secret);

        return $this->container->get('renderer')->render($response, "/endpoints/add.php", $this->template_variables);
    }

    public function edit($request, $response, $args) {
        $endpoints_service = $this->container->get('services')['endpoints'];
        $this->fetchCsrfVariables($request);

        $this->template_variables['endpoint_id'] = $args['id'];

        $endpoint = $endpoints_service->getById($args['id']);
        if ($endpoint != false) {
            $this->template_variables['endpoint_id'] = $endpoint->id();
            $this->template_variables['endpoint_name'] = $endpoint->name;
            $this->template_variables['endpoint_key'] = $endpoint->key;
            $this->template_variables['endpoint_secret'] = $endpoint->secret;
        }

        return $this->container->get('renderer')->render($response, "/endpoints/edit.php", $this->template_variables);
    }

    public function process_edit($request, $response, $args) {
        $endpoints_service = $this->container->get('services')['endpoints'];
        $this->fetchCsrfVariables($request);

        $endpoint_name = $request->getParam('name');
        $endpoint_key = $request->getParam('key');
        $endpoint_secret = $request->getParam('secret');

        $this->template_variables['endpoint_name'] = $endpoint_name;
        $this->template_variables['endpoint_key'] = $endpoint_key;
        $this->template_variables['endpoint_secret'] = $endpoint_secret;
        $this->template_variables['edit_attempted'] = true;

        //validate

        $this->template_variables['edited'] = $endpoints_service->update($args['id'], $endpoint_name, $endpoint_key, $endpoint_secret);

        return $this->container->get('renderer')->render($response, "/endpoints/edit.php", $this->template_variables);
    }

    public function delete($request, $response, $args) {
        $endpoints_service = $this->container->get('services')['endpoints'];
        $this->fetchCsrfVariables($request);

        $this->template_variables['endpoint_id'] = $args['id'];

        $endpoint = $endpoints_service->getById($args['id']);
        if ($endpoint != false) {
            $this->template_variables['endpoint_id'] = $endpoint->id();
            $this->template_variables['endpoint_name'] = $endpoint->name;
            $this->template_variables['endpoint_key'] = $endpoint->key;
            $this->template_variables['endpoint_secret'] = $endpoint->secret;
        }

        return $this->container->get('renderer')->render($response, "/endpoints/delete.php", $this->template_variables);
    }

    public function process_delete($request, $response, $args) {
        $endpoints_service = $this->container->get('services')['endpoints'];
        $this->fetchCsrfVariables($request);

        $this->template_variables['endpoint_id'] = $args['id'];

        $endpoint = $endpoints_service->getById($args['id']);
        if ($endpoint != false) {
            $this->template_variables['endpoint_id'] = $endpoint->id();
            $this->template_variables['endpoint_name'] = $endpoint->name;
            $this->template_variables['endpoint_key'] = $endpoint->key;
            $this->template_variables['endpoint_secret'] = $endpoint->secret;
        }

        $this->template_variables['deleted'] = $endpoints_service->deleteById($args['id']);

        return $this->container->get('renderer')->render($response, "/endpoints/delete.php", $this->template_variables);
    }
}