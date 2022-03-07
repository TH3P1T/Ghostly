<?php

namespace Ghostly\Controllers;

class Licenses extends \Ghostly\Controller
{
    public function __construct($container) {
        parent::__construct($container);

        $this->template_variables['product_id'] = "";
        $this->template_variables['license_key'] = "";
        $this->template_variables['license_customer_name'] = "";
        $this->template_variables['license_customer_email'] = "";
        $this->template_variables['license_purchase_date'] = "";
        $this->template_variables['license_order_reference'] = "";
        $this->template_variables['license_comments'] = "";
        $this->template_variables['license_hardware_id'] = "";
        $this->template_variables['license_expiration_date'] = "";
        $this->template_variables['license_disabled'] = "";

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

        return $this->container->get('renderer')->render($response, "/licenses/index.php", $this->template_variables);
    }

    public function ajax_list($request, $response, $args) {
        $licenses_service = $this->container->get('services')['licenses'];

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

        $data['total'] = $licenses_service->total($searchPhrase);
        $data['rows'] = $licenses_service->getPaged($current, $rowCount, $sort, $searchPhrase);

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
        $licenses_service = $this->container->get('services')['licenses'];
        $this->fetchCsrfVariables($request);

        $this->template_variables['license_key'] = $licenses_service->generateRandomSerial("LIC");
        $this->initProductList();

        return $this->container->get('renderer')->render($response, "/licenses/add.php", $this->template_variables);
    }

    public function process_add($request, $response, $args) {
        $products_service = $this->container->get('services')['products'];
        $licenses_service = $this->container->get('services')['licenses'];
        $this->fetchCsrfVariables($request);

        $product_id = $request->getParam('product_id');
        $license_key = $request->getParam('key');
        $license_customer_name = $request->getParam('customer_name');
        $license_customer_email = $request->getParam('customer_email');
        $license_purchase_date = $request->getParam('purchase_date');
        $license_order_reference = $request->getParam('order_reference');
        $license_comments = $request->getParam('comments');
        $license_hardware_id = $request->getParam('hardware_id');
        $license_expiration_date = $request->getParam('expiration_date');
        $license_disabled = ($request->getParam('disabled') == 1 ? 1 : 0);

        $purchase_date = new \DateTime($license_purchase_date, new \DateTimeZone($_SESSION['user_timezone']));
        $purchase_date->setTimezone(new \DateTimeZone("UTC"));

        $license_purchase_date = $purchase_date->format("Y-m-d");

        if (strlen($license_expiration_date) > 0) {
            $expiration_date = new \DateTime($license_expiration_date, new \DateTimeZone($_SESSION['user_timezone']));
            $expiration_date->setTimezone(new \DateTimeZone("UTC"));

            $license_expiration_date = $expiration_date->format("Y-m-d");
        }

        $this->template_variables['product_id'] = $product_id;
        $this->template_variables['license_key'] = $license_key;
        $this->template_variables['license_customer_name'] = $license_customer_name;
        $this->template_variables['license_customer_email'] = $license_customer_email;
        $this->template_variables['license_purchase_date'] = $license_purchase_date;
        $this->template_variables['license_order_reference'] = $license_order_reference;
        $this->template_variables['license_comments'] = $license_comments;
        $this->template_variables['license_hardware_id'] = $license_hardware_id;
        $this->template_variables['license_expiration_date'] = $license_expiration_date;
        $this->template_variables['license_disabled'] = $license_disabled;
        $this->template_variables['add_attempted'] = true;

        $this->initProductList();

        //validate

        $this->template_variables['added'] = $licenses_service->add(
            $product_id, $license_key, $license_customer_name, $license_customer_email, $license_order_reference, $license_purchase_date,
            $license_comments, $license_hardware_id, $license_expiration_date, $license_disabled);


        //restore timezone?
        $purchase_date = new \DateTime($this->template_variables['license_purchase_date'], new \DateTimeZone("UTC"));
        $purchase_date->setTimezone(new \DateTimeZone($_SESSION['user_timezone']));
        $this->template_variables['license_purchase_date'] = $purchase_date->format("Y-m-d");

        if (strlen($this->template_variables['license_expiration_date']) > 0) {
            $expiration_date = new \DateTime($this->template_variables['license_expiration_date'], new \DateTimeZone("UTC"));
            $expiration_date->setTimezone(new \DateTimeZone($_SESSION['user_timezone']));
            $this->template_variables['license_expiration_date'] = $expiration_date->format("Y-m-d");
        }

        return $this->container->get('renderer')->render($response, "/licenses/add.php", $this->template_variables);
    }

    public function view($request, $response, $args) {
        $licenses_service = $this->container->get('services')['licenses'];
        $this->fetchCsrfVariables($request);

        $this->template_variables['license_id'] = $args['id'];
        $license = $licenses_service->getById($args['id']);
        if ($license != false) {
            $this->template_variables['license_id'] = $license->id();
            $this->template_variables['product_id'] = $license->product_id;
            $this->template_variables['license_key'] = $license->key;
            $this->template_variables['license_customer_name'] = $license->customer_name;
            $this->template_variables['license_customer_email'] = $license->customer_email;
            $this->template_variables['license_purchase_date'] = $license->purchase_date;
            $this->template_variables['license_order_reference'] = $license->order_reference;
            $this->template_variables['license_comments'] = $license->comments;
            $this->template_variables['license_hardware_id'] = $license->hardware_id;
            $this->template_variables['license_expiration_date'] = $license->expiration_date;
            $this->template_variables['license_disabled'] = $license->disabled;

            $purchase_date = new \DateTime($this->template_variables['license_purchase_date'], new \DateTimeZone("UTC"));
            $purchase_date->setTimezone(new \DateTimeZone($_SESSION['user_timezone']));
            $this->template_variables['license_purchase_date'] = $purchase_date->format("Y-m-d");

            if (strlen($this->template_variables['license_expiration_date']) > 0 && 
                $this->template_variables['license_expiration_date'] != '0000-00-00 00:00:00') 
            {
                $expiration_date = new \DateTime($this->template_variables['license_expiration_date'], new \DateTimeZone("UTC"));
                $expiration_date->setTimezone(new \DateTimeZone($_SESSION['user_timezone']));
                $this->template_variables['license_expiration_date'] = $expiration_date->format("Y-m-d");
            }

            if ($this->template_variables['license_expiration_date'] == '0000-00-00 00:00:00')
            {
                $this->template_variables['license_expiration_date'] = '';
            }
        }

        $this->initProductList();

        return $this->container->get('renderer')->render($response, "/licenses/view.php", $this->template_variables);
    }

    public function edit($request, $response, $args) {
        $licenses_service = $this->container->get('services')['licenses'];
        $this->fetchCsrfVariables($request);

        $this->template_variables['license_id'] = $args['id'];

        $license = $licenses_service->getById($args['id']);
        if ($license != false) {
            $this->template_variables['license_id'] = $license->id();
            $this->template_variables['product_id'] = $license->product_id;
            $this->template_variables['license_key'] = $license->key;
            $this->template_variables['license_customer_name'] = $license->customer_name;
            $this->template_variables['license_customer_email'] = $license->customer_email;
            $this->template_variables['license_purchase_date'] = $license->purchase_date;
            $this->template_variables['license_order_reference'] = $license->order_reference;
            $this->template_variables['license_comments'] = $license->comments;
            $this->template_variables['license_hardware_id'] = $license->hardware_id;
            $this->template_variables['license_expiration_date'] = $license->expiration_date;
            $this->template_variables['license_disabled'] = $license->disabled;

            $purchase_date = new \DateTime($this->template_variables['license_purchase_date'], new \DateTimeZone("UTC"));
            $purchase_date->setTimezone(new \DateTimeZone($_SESSION['user_timezone']));
            $this->template_variables['license_purchase_date'] = $purchase_date->format("Y-m-d");

            if (strlen($this->template_variables['license_expiration_date']) > 0 && 
                $this->template_variables['license_expiration_date'] != '0000-00-00 00:00:00') 
            {
                $expiration_date = new \DateTime($this->template_variables['license_expiration_date'], new \DateTimeZone("UTC"));
                $expiration_date->setTimezone(new \DateTimeZone($_SESSION['user_timezone']));
                $this->template_variables['license_expiration_date'] = $expiration_date->format("Y-m-d");
            }

            if ($this->template_variables['license_expiration_date'] == '0000-00-00 00:00:00')
            {
                $this->template_variables['license_expiration_date'] = '';
            }
        }

        $this->initProductList();

        return $this->container->get('renderer')->render($response, "/licenses/edit.php", $this->template_variables);
    }

    public function process_edit($request, $response, $args) {
        $licenses_service = $this->container->get('services')['licenses'];
        $this->fetchCsrfVariables($request);

        $product_id = $request->getParam('product_id');
        $license_key = $request->getParam('key');
        $license_customer_name = $request->getParam('customer_name');
        $license_customer_email = $request->getParam('customer_email');
        $license_purchase_date = $request->getParam('purchase_date');
        $license_order_reference = $request->getParam('order_reference');
        $license_comments = $request->getParam('comments');
        $license_hardware_id = $request->getParam('hardware_id');
        $license_expiration_date = $request->getParam('expiration_date');
        $license_disabled = ($request->getParam('disabled') == 1 ? 1 : 0);

        $purchase_date = new \DateTime($license_purchase_date, new \DateTimeZone($_SESSION['user_timezone']));
        $purchase_date->setTimezone(new \DateTimeZone("UTC"));

        $license_purchase_date = $purchase_date->format("Y-m-d");

        if (strlen($license_expiration_date) > 0) {
            $expiration_date = new \DateTime($license_expiration_date, new \DateTimeZone($_SESSION['user_timezone']));
            $expiration_date->setTimezone(new \DateTimeZone("UTC"));

            $license_expiration_date = $expiration_date->format("Y-m-d");
        }

        $this->template_variables['license_id'] = $args['id'];
        $this->template_variables['product_id'] = $product_id;
        $this->template_variables['license_key'] = $license_key;
        $this->template_variables['license_customer_name'] = $license_customer_name;
        $this->template_variables['license_customer_email'] = $license_customer_email;
        $this->template_variables['license_purchase_date'] = $license_purchase_date;
        $this->template_variables['license_order_reference'] = $license_order_reference;
        $this->template_variables['license_comments'] = $license_comments;
        $this->template_variables['license_hardware_id'] = $license_hardware_id;
        $this->template_variables['license_expiration_date'] = $license_expiration_date;
        $this->template_variables['license_disabled'] = $license_disabled;
        $this->template_variables['edit_attempted'] = true;

        $this->initProductList();

        //validate

        $this->template_variables['edited'] = $licenses_service->update($args['id'],
            $product_id, $license_key, $license_customer_name, $license_customer_email, $license_order_reference, $license_purchase_date,
            $license_comments, $license_hardware_id, $license_expiration_date, $license_disabled);

        return $this->container->get('renderer')->render($response, "/licenses/edit.php", $this->template_variables);
    }

    public function delete($request, $response, $args) {
        $licenses_service = $this->container->get('services')['licenses'];
        $this->fetchCsrfVariables($request);

        $this->template_variables['license_id'] = $args['id'];

        $license = $licenses_service->getById($args['id']);
        if ($license != false) {
            $this->template_variables['license_id'] = $license->id();
            $this->template_variables['product_id'] = $license->product_id;
            $this->template_variables['license_key'] = $license->key;
            $this->template_variables['license_customer_name'] = $license->customer_name;
            $this->template_variables['license_customer_email'] = $license->customer_email;
            $this->template_variables['license_purchase_date'] = $license->purchase_date;
            $this->template_variables['license_order_reference'] = $license->order_reference;
            $this->template_variables['license_comments'] = $license->comments;
            $this->template_variables['license_hardware_id'] = $license->hardware_id;
            $this->template_variables['license_expiration_date'] = $license->expiration_date;
            $this->template_variables['license_disabled'] = $license->disabled;

            $purchase_date = new \DateTime($this->template_variables['license_purchase_date'], new \DateTimeZone("UTC"));
            $purchase_date->setTimezone(new \DateTimeZone($_SESSION['user_timezone']));
            $this->template_variables['license_purchase_date'] = $purchase_date->format("Y-m-d");

            if (strlen($this->template_variables['license_expiration_date']) > 0) {
                $expiration_date = new \DateTime($this->template_variables['license_expiration_date'], new \DateTimeZone("UTC"));
                $expiration_date->setTimezone(new \DateTimeZone($_SESSION['user_timezone']));
                $this->template_variables['license_expiration_date'] = $expiration_date->format("Y-m-d");
            }
        }

        $this->initProductList();

        return $this->container->get('renderer')->render($response, "/licenses/delete.php", $this->template_variables);
    }

    public function process_delete($request, $response, $args) {
        $licenses_service = $this->container->get('services')['licenses'];
        $this->fetchCsrfVariables($request);

        $this->template_variables['license_id'] = $args['id'];
        $this->template_variables['delete_attempted'] = true;

        $license = $licenses_service->getById($args['id']);
        if ($license != false) {
            $this->template_variables['license_id'] = $license->id();
            $this->template_variables['product_id'] = $license->product_id;
            $this->template_variables['license_key'] = $license->key;
            $this->template_variables['license_customer_name'] = $license->customer_name;
            $this->template_variables['license_customer_email'] = $license->customer_email;
            $this->template_variables['license_purchase_date'] = $license->purchase_date;
            $this->template_variables['license_order_reference'] = $license->order_reference;
            $this->template_variables['license_comments'] = $license->comments;
            $this->template_variables['license_hardware_id'] = $license->hardware_id;
            $this->template_variables['license_expiration_date'] = $license->expiration_date;
            $this->template_variables['license_disabled'] = $license->disabled;

            $purchase_date = new \DateTime($this->template_variables['license_purchase_date'], new \DateTimeZone("UTC"));
            $purchase_date->setTimezone(new \DateTimeZone($_SESSION['user_timezone']));
            $this->template_variables['license_purchase_date'] = $purchase_date->format("Y-m-d");

            if (strlen($this->template_variables['license_expiration_date']) > 0) {
                $expiration_date = new \DateTime($this->template_variables['license_expiration_date'], new \DateTimeZone("UTC"));
                $expiration_date->setTimezone(new \DateTimeZone($_SESSION['user_timezone']));
                $this->template_variables['license_expiration_date'] = $expiration_date->format("Y-m-d");
            }
        }

        $this->initProductList();
        $this->template_variables['deleted'] = $licenses_service->deleteById($args['id']);

        return $this->container->get('renderer')->render($response, "/licenses/delete.php", $this->template_variables);
    }

    protected function initProductList() {
        $products_service = $this->container->get('services')['products'];

        $products = $products_service->getAll();
        if ($products === false) {
            $products = array();
        }

        $this->template_variables['product_list'] = $products;
    }
}