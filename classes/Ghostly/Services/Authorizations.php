<?php

namespace Ghostly\Services;

class Authorizations {
    public function add($license_key, $hardware_id, $ip_address, $response, $success) {
        $authorization = \ORM::forTable('authorizations')->create();

        $authorization->license_key = $license_key;
        $authorization->hardware_id = $hardware_id;
        $authorization->ip_address = $ip_address;
        $authorization->response = $response;
        $authorization->success = $success;

        $authorization->set_expr('created', 'UTC_TIMESTAMP()');
        
        try {
            return $authorization->save();
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    public function getById($id) {
        try {
            return \ORM::forTable('authorizations')->findOne($id);
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    public function getAll() {
        try {
            return \ORM::forTable('authorizations')->findArray();
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
            $authorizations = \ORM::forTable('authorizations');
            
            if ($row_count > 0) {
                $authorizations = $authorizations->offset(($page - 1) * $row_count)->limit($row_count);
            }

            if (is_string($search_phrase) && strlen($search_phrase) > 0) {
                $authorizations = $authorizations->where_like('license_key', '%' . $search_phrase . '%');
            }

            if (is_array($sort)) {
                if (array_key_exists('license_key', $sort)) {
                    if ($sort['license_key'] == 'asc') {
                        $authorizations = $authorizations->order_by_asc('license_key');
                    }
                    else {
                        $authorizations = $authorizations->order_by_desc('license_key');
                    }
                }
                elseif (array_key_exists('hardware_id', $sort)) {
                    if ($sort['hardware_id'] == 'asc') {
                        $authorizations = $authorizations->order_by_asc('hardware_id');
                    }
                    else {
                        $authorizations = $authorizations->order_by_desc('hardware_id');
                    }
                }
                elseif (array_key_exists('id', $sort)) {
                    if ($sort['id'] == 'asc') {
                        $authorizations = $authorizations->order_by_asc('id');
                    }
                    else {
                        $authorizations = $authorizations->order_by_desc('id');
                    }
                }
            }
            return $authorizations->findArray();
        }
        catch (\PDOException $Exception) {
            return array();
        }
    }

    public function total($search_phrase = "") {
        try {
            if (is_string($search_phrase) && strlen($search_phrase) > 0) {
                return \ORM::forTable('authorizations')->where_like('license_key', '%' . $search_phrase . '%')->count();
            }
            
            return \ORM::forTable('authorizations')->count();
        }
        catch( \PDOException $Exception ) {
            return 0;
        }
    }

    public function latestSummaryValid($limit = 25) {
        try {
            $products = \ORM::forTable('authorizations')
                ->table_alias('t1')
                ->select('t1.*')
                ->where_equal('success', 1)
                ->limit($limit)
                ->order_by_desc('t1.id');
            
            return $products->findArray();
        }
        catch (\PDOException $Exception) {
            return array();
        }
    }

    public function latestSummaryInvalid($limit = 25) {
        try {
            $products = \ORM::forTable('authorizations')
                ->table_alias('t1')
                ->select('t1.*')
                ->where_equal('success', 0)
                ->limit($limit)
                ->order_by_desc('t1.id');
            
            return $products->findArray();
        }
        catch (\PDOException $Exception) {
            return array();
        }
    }

    public function totalValid() {
        try {
            return \ORM::forTable('authorizations')->where_equal('success', 1)->count();
        }
        catch( \PDOException $Exception ) {
            return 0;
        }
    }

    public function totalInvalid() {
        try {
            return \ORM::forTable('authorizations')->where_not_equal('success', 1)->count();
        }
        catch( \PDOException $Exception ) {
            return 0;
        }
    }
}