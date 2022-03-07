<?php

namespace Ghostly\Controllers;

class Users extends \Ghostly\Controller
{
    public function __construct($container) {
        parent::__construct($container);

        $this->template_variables['user_id'] = "";
        $this->template_variables['user_username'] = "";
        $this->template_variables['user_password'] = "";
        $this->template_variables['user_notes'] = "";
        $this->template_variables['user_timezone'] = "";

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

        return $this->container->get('renderer')->render($response, "/users/index.php", $this->template_variables);
    }

    public function ajax_list($request, $response, $args) {
        $users_service = $this->container->get('services')['users'];

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

        $data['total'] = $users_service->total($searchPhrase);
        $data['rows'] = $users_service->getPaged($current, $rowCount, $sort, $searchPhrase);

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

        return $this->container->get('renderer')->render($response, "/users/add.php", $this->template_variables);
    }

    public function process_add($request, $response, $args) {
        $users_service = $this->container->get('services')['users'];

        $this->fetchCsrfVariables($request);

        $username = $request->getParam('username');
        $password = $request->getParam('password');
        $notes = $request->getParam('notes');
        $user_tz = $request->getParam('timezone');

        $this->template_variables['user_username'] = $username;
        $this->template_variables['user_password'] = $password;
        $this->template_variables['user_notes'] = $notes;
        $this->template_variables['user_timezone'] = $user_tz;
        $this->template_variables['add_attempted'] = true;

        //validation

        $this->template_variables['validation_failed'] = false;
        $this->template_variables['added'] = $users_service->add($username, $password, $notes, $user_tz);

        return $this->container->get('renderer')->render($response, "/users/add.php", $this->template_variables);
    }

    public function view($request, $response, $args) {
        $users_service = $this->container->get('services')['users'];
        $this->fetchCsrfVariables($request);

        $this->template_variables['user_id'] = $args['id'];
        $user = $users_service->getById($args['id']);
        if ($user != false) {
            $this->template_variables['user_id'] = $user->id();
            $this->template_variables['user_username'] = $user->username;
            $this->template_variables['user_notes'] = $user->notes;
            $this->template_variables['user_timezone'] = $user->timezone;
        }

        return $this->container->get('renderer')->render($response, "/users/view.php", $this->template_variables);
    }

    public function edit($request, $response, $args) {
        $users_service = $this->container->get('services')['users'];

        $this->fetchCsrfVariables($request);
        $this->template_variables['user_id'] = $args['id'];

        $user = $users_service->getById($args['id']);
        if ($user != false) {
            $this->template_variables['user_id'] = $user->id();
            $this->template_variables['user_username'] = $user->username;
            $this->template_variables['user_notes'] = $user->notes;
            $this->template_variables['user_timezone'] = $user->timezone;
        }

        return $this->container->get('renderer')->render($response, "/users/edit.php", $this->template_variables);
    }

    public function process_edit($request, $response, $args) {
        $users_service = $this->container->get('services')['users'];

        $this->fetchCsrfVariables($request);

        $username = $request->getParam('username');
        $password = $request->getParam('password');
        $notes = $request->getParam('notes');
        $user_tz = $request->getParam('timezone');

        $this->template_variables['user_id'] = $args['id'];
        $this->template_variables['user_username'] = $username;
        $this->template_variables['user_password'] = $password;
        $this->template_variables['user_notes'] = $notes;
        $this->template_variables['user_timezone'] = $user_tz;
        $this->template_variables['edit_attempted'] = true;

        //validation

        $this->template_variables['validation_failed'] = false;
        $this->template_variables['edited'] = $users_service->update($args['id'], $password, $notes, $user_tz);

        return $this->container->get('renderer')->render($response, "/users/edit.php", $this->template_variables);
    }

    public function delete($request, $response, $args) {
        $users_service = $this->container->get('services')['users'];

        $this->fetchCsrfVariables($request);
        $this->template_variables['user_id'] = $args['id'];

        $user = $users_service->getById($args['id']);
        if ($user != false) {
            $this->template_variables['user_id'] = $user->id();
            $this->template_variables['user_username'] = $user->username;
            $this->template_variables['user_notes'] = $user->notes;
            $this->template_variables['user_timezone'] = $user->timezone;
        }

        return $this->container->get('renderer')->render($response, "/users/delete.php", $this->template_variables);
    }

    public function process_delete($request, $response, $args) {
        $users_service = $this->container->get('services')['users'];

        $this->fetchCsrfVariables($request);
        $this->template_variables['user_id'] = $args['id'];
        $this->template_variables['delete_attempted'] = true;

        $user = $users_service->getById($args['id']);
        if ($user != false) {
            $this->template_variables['user_id'] = $user->id();
            $this->template_variables['user_username'] = $user->username;
            $this->template_variables['user_notes'] = $user->notes;
            $this->template_variables['user_timezone'] = $user->timezone;
        }

        $this->template_variables['deleted'] = $users_service->deleteById($args['id']);

        return $this->container->get('renderer')->render($response, "/users/delete.php", $this->template_variables);
    }
}
