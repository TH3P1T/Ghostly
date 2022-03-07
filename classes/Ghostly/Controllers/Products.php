<?php

namespace Ghostly\Controllers;

class Products extends \Ghostly\Controller
{
    public function __construct($container) {
        parent::__construct($container);

        $this->template_variables['product_id'] = "";
        $this->template_variables['product_name'] = "";
        $this->template_variables['product_short_name'] = "";
        $this->template_variables['product_description'] = "";
        $this->template_variables['product_private_key'] = "";
        $this->template_variables['product_public_key'] = "";

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

        return $this->container->get('renderer')->render($response, "/products/index.php", $this->template_variables);
    }

    public function ajax_list($request, $response, $args) {
        $products_service = $this->container->get('services')['products'];

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

        $data['total'] = $products_service->total($searchPhrase);
        $data['rows'] = $products_service->getPaged($current, $rowCount, $sort, $searchPhrase);

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
        $this->fetchCsrfVariables($request);

        return $this->container->get('renderer')->render($response, "/products/add.php", $this->template_variables);
    }

    public function process_add($request, $response, $args) {
        $products_service = $this->container->get('services')['products'];

        $this->fetchCsrfVariables($request);

        $full_name = $request->getParam('name');
        $short_name = $request->getParam('short_name');
        $description = $request->getParam('description');
        $private_key = $request->getParam('private_key');
        $public_key = $request->getParam('public_key');

        $this->template_variables['product_name'] = $full_name;
        $this->template_variables['product_short_name'] = $short_name;
        $this->template_variables['product_description'] = $description;
        $this->template_variables['product_private_key'] = $private_key;
        $this->template_variables['product_public_key'] = $public_key;
        $this->template_variables['add_attempted'] = true;

        if (!$products_service->validateName($full_name) ||
            !$products_service->validateShortName($short_name) ||
            !$products_service->validateDescription($description) ||
            !$products_service->validatePrivateKey($private_key) ||
            !$products_service->validatePublicKey($public_key))
        {
            $this->template_variables['validation_failed'] = true;
            return $this->container->get('renderer')->render($response, "/products/add.php", $this->template_variables);
        }

        $this->template_variables['validation_failed'] = false;
        $this->template_variables['added'] = $products_service->add($full_name, $short_name, $description, $private_key, $public_key);

        return $this->container->get('renderer')->render($response, "/products/add.php", $this->template_variables);
    }

    public function view($request, $response, $args) {
        $products_service = $this->container->get('services')['products'];

        $this->fetchCsrfVariables($request);

        $this->template_variables['product_id'] = $args['id'];
        $product = $products_service->getById($args['id']);
        if ($product != false) {
            $this->template_variables['product_id'] = $product->id();
            $this->template_variables['product_name'] = $product->name;
            $this->template_variables['product_short_name'] = $product->short_name;
            $this->template_variables['product_description'] = $product->description;
            $this->template_variables['product_private_key'] = $product->private_key;
            $this->template_variables['product_public_key'] = $product->public_key;
        }

        return $this->container->get('renderer')->render($response, "/products/view.php", $this->template_variables);
    }

    public function edit($request, $response, $args) {
        $products_service = $this->container->get('services')['products'];

        $this->fetchCsrfVariables($request);

        $this->template_variables['product_id'] = $args['id'];

        $product = $products_service->getById($args['id']);
        if ($product != false) {
            $this->template_variables['product_id'] = $product->id();
            $this->template_variables['product_name'] = $product->name;
            $this->template_variables['product_short_name'] = $product->short_name;
            $this->template_variables['product_description'] = $product->description;
            $this->template_variables['product_private_key'] = $product->private_key;
            $this->template_variables['product_public_key'] = $product->public_key;
        }

        return $this->container->get('renderer')->render($response, "/products/edit.php", $this->template_variables);
    }

    public function process_edit($request, $response, $args) {
        $products_service = $this->container->get('services')['products'];

        $this->fetchCsrfVariables($request);

        $full_name = $request->getParam('name');
        $short_name = $request->getParam('short_name');
        $description = $request->getParam('description');
        $private_key = $request->getParam('private_key');
        $public_key = $request->getParam('public_key');

        $this->template_variables['product_id'] = $args['id'];
        $this->template_variables['product_name'] = $full_name;
        $this->template_variables['product_short_name'] = $short_name;
        $this->template_variables['product_description'] = $description;
        $this->template_variables['product_private_key'] = $private_key;
        $this->template_variables['product_public_key'] = $public_key;
        $this->template_variables['edit_attempted'] = true;

        if (!$products_service->validateName($full_name) ||
            !$products_service->validateShortName($short_name) ||
            !$products_service->validateDescription($description) ||
            !$products_service->validatePrivateKey($private_key) ||
            !$products_service->validatePublicKey($public_key))
        {
            $this->template_variables['validation_failed'] = true;
            return $this->container->get('renderer')->render($response, "/products/edit.php", $this->template_variables);
        }

        $this->template_variables['validation_failed'] = false;
        $this->template_variables['edited'] = $products_service->update($args['id'], $full_name, $short_name, $description, $private_key, $public_key);

        return $this->container->get('renderer')->render($response, "/products/edit.php", $this->template_variables);
    }

    public function delete($request, $response, $args) {
        $products_service = $this->container->get('services')['products'];

        $this->fetchCsrfVariables($request);

        $this->template_variables['product_id'] = $args['id'];

        $product = $products_service->getById($args['id']);
        if ($product != false) {
            $this->template_variables['product_id'] = $product->id();
            $this->template_variables['product_name'] = $product->name;
            $this->template_variables['product_short_name'] = $product->short_name;
            $this->template_variables['product_description'] = $product->description;
            $this->template_variables['product_private_key'] = $product->private_key;
            $this->template_variables['product_public_key'] = $product->public_key;
        }

        return $this->container->get('renderer')->render($response, "/products/delete.php", $this->template_variables);
    }

    public function process_delete($request, $response, $args) {
        $products_service = $this->container->get('services')['products'];

        $this->fetchCsrfVariables($request);

        $this->template_variables['product_id'] = $args['id'];
        $this->template_variables['delete_attempted'] = true;

        $product = $products_service->getById($args['id']);
        if ($product != false) {
            $this->template_variables['product_id'] = $product->id();
            $this->template_variables['product_name'] = $product->name;
            $this->template_variables['product_short_name'] = $product->short_name;
            $this->template_variables['product_description'] = $product->description;
            $this->template_variables['product_private_key'] = $product->private_key;
            $this->template_variables['product_public_key'] = $product->public_key;
        }

        $this->template_variables['deleted'] = $products_service->deleteById($args['id']);

        return $this->container->get('renderer')->render($response, "/products/delete.php", $this->template_variables);
    }
}
