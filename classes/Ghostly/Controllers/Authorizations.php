<?php

namespace Ghostly\Controllers;

class Authorizations extends \Ghostly\Controller
{
    public function __construct($container) {
        parent::__construct($container);

        $this->template_variables['authorization_id'] = "";
        $this->template_variables['authorization_license_key'] = "";
        $this->template_variables['authorization_hardware_id'] = "";
        $this->template_variables['authorization_ip_address'] = "";
        $this->template_variables['authorization_created'] = "";
        $this->template_variables['authorization_response'] = "";
        $this->template_variables['authorization_success'] = "";
    }

    public function home($request, $response, $args) {
        $this->fetchCsrfVariables($request);

        return $this->container->get('renderer')->render($response, "/authorizations/index.php", $this->template_variables);
    }

    public function ajax_list($request, $response, $args) {
        $authorizations_service = $this->container->get('services')['authorizations'];

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

        $data['total'] = $authorizations_service->total($searchPhrase);
        $data['rows'] = $authorizations_service->getPaged($current, $rowCount, $sort, $searchPhrase);

        foreach ($data['rows'] as $key => $value)
        {
            if (array_key_exists('success', $value))
            {
                $value['status'] = $value['success'];
            }

            foreach ($value as $v_key => $v_value)
            {
                $value[$v_key] = htmlspecialchars($v_value);
            }

            $data['rows'][$key] = $value;
        }

        return $response->withJson($data);
    }

    public function view($request, $response, $args) {
        $authorizations_service = $this->container->get('services')['authorizations'];

        $this->fetchCsrfVariables($request);

        $this->template_variables['authorization_id'] = $args['id'];
        $authorization = $authorizations_service->getById($args['id']);
        if ($authorization != false) {
            $this->template_variables['authorization_id'] = $authorization->id();
            $this->template_variables['authorization_license_key'] = $authorization->license_key;
            $this->template_variables['authorization_hardware_id'] = $authorization->hardware_id;
            $this->template_variables['authorization_ip_address'] = $authorization->ip_address;
            $this->template_variables['authorization_created'] = $authorization->created;
            $this->template_variables['authorization_response'] = $authorization->response;
            $this->template_variables['authorization_success'] = $authorization->success;
        }

        return $this->container->get('renderer')->render($response, "/authorizations/view.php", $this->template_variables);
    }
}
