<?php

namespace Ghostly\Services;

class Products {
    public function add($name, $short_name, $description, $private_key, $public_key) {
        $product = \ORM::forTable('products')->create();

        $product->name = $name;
        $product->short_name = $short_name;
        $product->description = $description;
        $product->private_key = $private_key;
        $product->public_key = $public_key;
        $product->set_expr('created', 'UTC_TIMESTAMP()');
        $product->set_expr('updated', 'UTC_TIMESTAMP()');
        try {
            return $product->save();
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    public function update($id, $name, $short_name, $description, $private_key, $public_key) {
        try {
            $product = \ORM::forTable('products')->findOne($id);

            if ($product === false) {
                return false;
            }

            $product->name = $name;
            $product->short_name = $short_name;
            $product->description = $description;
            $product->private_key = $private_key;
            $product->public_key = $public_key;
            $product->set_expr('updated', 'UTC_TIMESTAMP()');

            return $product->save();
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    public function deleteById($id) {
        try {
            $product = \ORM::forTable('products')->findOne($id);

            if ($product === false) {
                return false;
            }

            return $product->delete();
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    public function getById($id) {
        try {
            return \ORM::forTable('products')->findOne($id);
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    public function getAll() {
        try {
            return \ORM::forTable('products')->findArray();
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
            $products = \ORM::forTable('products');
            
            if ($row_count > 0) {
                $products = $products->offset(($page - 1) * $row_count)->limit($row_count);
            }

            if (is_string($search_phrase) && strlen($search_phrase) > 0) {
                $products = $products->where_like('name', '%' . $search_phrase . '%');
            }

            if (is_array($sort)) {
                if (array_key_exists('name', $sort)) {
                    if ($sort['name'] == 'asc') {
                        $products = $products->order_by_asc('name');
                    }
                    else {
                        $products = $products->order_by_desc('name');
                    }
                }
                elseif (array_key_exists('description', $sort)) {
                    if ($sort['description'] == 'asc') {
                        $products = $products->order_by_asc('description');
                    }
                    else {
                        $products = $products->order_by_desc('description');
                    }
                }
                elseif (array_key_exists('id', $sort)) {
                    if ($sort['id'] == 'asc') {
                        $products = $products->order_by_asc('id');
                    }
                    else {
                        $products = $products->order_by_desc('id');
                    }
                }
            }
            return $products->findArray();
        }
        catch (\PDOException $Exception) {
            return array();
        }
    }

    public function total($search_phrase = "") {
        try {
            if (is_string($search_phrase) && strlen($search_phrase) > 0) {
                return \ORM::forTable('products')->where_like('name', '%' . $search_phrase . '%')->count();
            }
            
            return \ORM::forTable('products')->count();
        }
        catch( \PDOException $Exception ) {
            return 0;
        }
    }


    public function latestSummary($limit = 25) {
        try {
            $products = \ORM::forTable('products')
                ->table_alias('t1')
                ->select('t1.*')
                ->select_expr('COUNT(t2.id)', 'license_count')
                ->join('licenses', array('t1.id', '=', 't2.product_id'), 't2')
                ->group_by('t1.id')
                ->limit($limit)
                ->order_by_desc('t1.id');
            
            return $products->findArray();
        }
        catch (\PDOException $Exception) {
            return array();
        }
    }

    public function validateName($name) {
        return true;
    }

    public function validateShortName($short_name) {
        return true;
    }

    public function validateDescription($description) {
        return true;
    }

    public function validatePrivateKey($private_key) {
        return true;
    }

    public function validatePublicKey($public_key) {
        return true;
    }
}