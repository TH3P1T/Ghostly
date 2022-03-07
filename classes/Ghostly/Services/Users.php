<?php

namespace Ghostly\Services;

class Users {
    public function add($username, $password, $notes, $timezone) {
        $user = \ORM::forTable('users')->create();

        $user->username = $username;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->role = 'ADMIN';
        $user->notes = $notes;
        $user->timezone = $timezone;

        $user->set_expr('created', 'UTC_TIMESTAMP()');
        $user->set_expr('updated', 'UTC_TIMESTAMP()');
        
        try {
            return $user->save();
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    public function update($id, $password, $notes, $timezone) {
        try {
            $user = \ORM::forTable('users')->findOne($id);

            if ($user === false) {
                return false;
            }

            if (strlen($password) > 4) {
                $user->password = password_hash($password, PASSWORD_DEFAULT);
            }
            
            $user->notes = $notes;
            $user->timezone = $timezone;
            $user->set_expr('updated', 'UTC_TIMESTAMP()');

            return $user->save();
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    public function deleteById($id) {
        try {
            $user = \ORM::forTable('users')->findOne($id);

            if ($user === false) {
                return false;
            }

            return $user->delete();
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    public function getById($id) {
        try {
            return \ORM::forTable('users')->findOne($id);
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    public function getAll() {
        try {
            return \ORM::forTable('users')->findArray();
        }
        catch( \PDOException $Exception ) {
            return array();
        }
    }

    public function getPaged($page, $row_count, $sort, $search_phrase) {
        if ($page < 1) {
            $page = 1;
        }

        try {
            $users = \ORM::forTable('users');
            
            if ($row_count > 0) {
                $users = $users->offset(($page - 1) * $row_count)->limit($row_count);
            }

            if (is_string($search_phrase) && strlen($search_phrase) > 0) {
                $users = $users->where_like('username', '%' . $search_phrase . '%');
            }

            if (is_array($sort)) {
                if (array_key_exists('username', $sort)) {
                    if ($sort['username'] == 'asc') {
                        $users = $users->order_by_asc('username');
                    }
                    else {
                        $users = $users->order_by_desc('username');
                    }
                }
                elseif (array_key_exists('notes', $sort)) {
                    if ($sort['notes'] == 'asc') {
                        $users = $users->order_by_asc('notes');
                    }
                    else {
                        $users = $users->order_by_desc('notes');
                    }
                }
                elseif (array_key_exists('id', $sort)) {
                    if ($sort['id'] == 'asc') {
                        $users = $users->order_by_asc('id');
                    }
                    else {
                        $users = $users->order_by_desc('id');
                    }
                }
            }
            return $users->findArray();
        }
        catch (\PDOException $Exception) {
            return array();
        }
    }

    public function total($search_phrase) {
        try {
            if (is_string($search_phrase) && strlen($search_phrase) > 0) {
                return \ORM::forTable('users')->where_like('username', '%' . $search_phrase . '%')->count();
            }
            
            return \ORM::forTable('users')->count();
        }
        catch( \PDOException $Exception ) {
            return 0;
        }
    }
}