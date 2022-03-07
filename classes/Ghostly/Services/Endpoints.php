<?php

namespace Ghostly\Services;

class Endpoints {
    public function add($name, $key, $secret) {
        try {
            $endpoint = \ORM::forTable('endpoints')->create();

            $endpoint->name = $name;
            $endpoint->key = $key;
            $endpoint->secret = $secret;

            $endpoint->set_expr('created', 'UTC_TIMESTAMP()');
            $endpoint->set_expr('updated', 'UTC_TIMESTAMP()');

            return $endpoint->save();
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    public function update($id, $name, $key, $secret) {
        try {
            $endpoint = \ORM::forTable('endpoints')->findOne($id);

            if ($endpoint === false) {
                return false;
            }

            $endpoint->name = $name;
            $endpoint->key = $key;
            $endpoint->secret = $secret;

            $endpoint->set_expr('updated', 'UTC_TIMESTAMP()');

            return $endpoint->save();
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    public function deleteById($id) {
        try {
            $endpoint = \ORM::forTable('endpoints')->findOne($id);

            if ($endpoint === false) {
                return false;
            }

            return $endpoint->delete();
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    public function getById($id) {
        try {
            return \ORM::forTable('endpoints')->findOne($id);
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    public function getByName($name) {
        try {
            return \ORM::forTable('endpoints')->where_like('name', $name)->findOne();
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    public function getPaged($page, $row_count, $sort, $search_phrase) {
        if ($page < 1) {
            $page = 1;
        }

        try {
            $endpoints = \ORM::forTable('endpoints');
            
            if ($row_count > 0) {
                $endpoints = $endpoints->offset(($page - 1) * $row_count)->limit($row_count);
            }

            if (is_string($search_phrase) && strlen($search_phrase) > 0) {
                $endpoints = $endpoints->where_like('name', '%' . $search_phrase . '%');
            }

            if (is_array($sort)) {
                if (array_key_exists('name', $sort)) {
                    if ($sort['name'] == 'asc') {
                        $endpoints = $endpoints->order_by_asc('name');
                    }
                    else {
                        $endpoints = $endpoints->order_by_desc('name');
                    }
                }
                elseif (array_key_exists('secret', $sort)) {
                    if ($sort['secret'] == 'asc') {
                        $endpoints = $endpoints->order_by_asc('secret');
                    }
                    else {
                        $endpoints = $endpoints->order_by_desc('secret');
                    }
                }
                elseif (array_key_exists('id', $sort)) {
                    if ($sort['id'] == 'asc') {
                        $endpoints = $endpoints->order_by_asc('id');
                    }
                    else {
                        $endpoints = $endpoints->order_by_desc('id');
                    }
                }
                elseif (array_key_exists('key', $sort)) {
                    if ($sort['key'] == 'asc') {
                        $endpoints = $endpoints->order_by_asc('key');
                    }
                    else {
                        $endpoints = $endpoints->order_by_desc('key');
                    }
                }
            }
            return $endpoints->findArray();
        }
        catch (\PDOException $Exception) {
            return array();
        }
    }

    public function total($search_phrase) {
        try {
            if (is_string($search_phrase) && strlen($search_phrase) > 0) {
                return \ORM::forTable('endpoints')->where_like('name', '%' . $search_phrase . '%')->count();
            }
            
            return \ORM::forTable('endpoints')->count();
        }
        catch( \PDOException $Exception ) {
            return 0;
        }
    }
}