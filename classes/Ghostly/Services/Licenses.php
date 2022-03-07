<?php

namespace Ghostly\Services;

class Licenses {
    public function generateRandomSerial($prefix = "") {
        if (strlen($prefix) <= 0) {
            return strtoupper(bin2hex(random_bytes(3)) . "-" . bin2hex(random_bytes(3)) . "-" . bin2hex(random_bytes(3)) . "-" . bin2hex(random_bytes(3)));
        }

        return strtoupper(substr($prefix . bin2hex(random_bytes(3)), 0, 6) . "-" . bin2hex(random_bytes(3)) . "-" . bin2hex(random_bytes(3)) . "-" . bin2hex(random_bytes(3)));
    }

    public function add($product_id, $key, $customer_name, $customer_email, $order_reference, $purchase_date, $comments, $hardware_id, $expiration_date, $disabled) {
        try {
            $license = \ORM::forTable('licenses')->create();

            $license->product_id = $product_id;
            $license->key = $key;
            $license->customer_name = $customer_name;
            $license->customer_email = $customer_email;
            $license->purchase_date = $purchase_date;
            $license->order_reference = $order_reference;
            $license->comments = $comments;
            $license->hardware_id = $hardware_id;
            $license->expiration_date = $expiration_date;
            $license->disabled = $disabled;

            $license->set_expr('created', 'UTC_TIMESTAMP()');
            $license->set_expr('updated', 'UTC_TIMESTAMP()');

            return $license->save();
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    public function update($id, $product_id, $key, $customer_name, $customer_email, $order_reference, $purchase_date, $comments, $hardware_id, $expiration_date, $disabled) {
        try {
            $license = \ORM::forTable('licenses')->findOne($id);

            if ($license === false) {
                return false;
            }

            $license->product_id = $product_id;
            $license->key = $key;
            $license->customer_name = $customer_name;
            $license->customer_email = $customer_email;
            $license->purchase_date = $purchase_date;
            $license->order_reference = $order_reference;
            $license->comments = $comments;
            $license->hardware_id = $hardware_id;
            $license->expiration_date = $expiration_date;
            $license->disabled = $disabled;

            $license->set_expr('updated', 'UTC_TIMESTAMP()');

            return $license->save();
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    public function deleteById($id) {
        try {
            $license = \ORM::forTable('licenses')->findOne($id);

            if ($license === false) {
                return false;
            }

            return $license->delete();
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    public function getById($id) {
        try {
            return \ORM::forTable('licenses')->findOne($id);
        }
        catch( \PDOException $Exception ) {
            return false;
        }
    }

    public function getByKey($key) {
        try {
            return \ORM::forTable('licenses')->where_like('key', $key)->findOne();
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
            $licenses = \ORM::forTable('licenses')
                ->table_alias('t1')
                ->select('t1.*')
                ->select('t2.name', 'product_name')
                ->join('products', array('t1.product_id', '=', 't2.id'), 't2');
            
            if ($row_count > 0) {
                $licenses = $licenses->offset(($page - 1) * $row_count)->limit($row_count);
            }

            if (is_string($search_phrase) && strlen($search_phrase) > 0) {
                $licenses = $licenses->where_like('customer_name', '%' . $search_phrase . '%');
            }

            if (is_array($sort)) {
                if (array_key_exists('customer_name', $sort)) {
                    if ($sort['customer_name'] == 'asc') {
                        $licenses = $licenses->order_by_asc('customer_name');
                    }
                    else {
                        $licenses = $licenses->order_by_desc('customer_name');
                    }
                }
                elseif (array_key_exists('product_name', $sort)) {
                    if ($sort['product_name'] == 'asc') {
                        $licenses = $licenses->order_by_asc('product_name');
                    }
                    else {
                        $licenses = $licenses->order_by_desc('product_name');
                    }
                }
                elseif (array_key_exists('id', $sort)) {
                    if ($sort['id'] == 'asc') {
                        $licenses = $licenses->order_by_asc('id');
                    }
                    else {
                        $licenses = $licenses->order_by_desc('id');
                    }
                }
                elseif (array_key_exists('key', $sort)) {
                    if ($sort['key'] == 'asc') {
                        $licenses = $licenses->order_by_asc('key');
                    }
                    else {
                        $licenses = $licenses->order_by_desc('key');
                    }
                }
                elseif (array_key_exists('expiration_date', $sort)) {
                    if ($sort['expiration_date'] == 'asc') {
                        $licenses = $licenses->order_by_asc('expiration_date');
                    }
                    else {
                        $licenses = $licenses->order_by_desc('expiration_date');
                    }
                }
            }
            return $licenses->findArray();
        }
        catch (\PDOException $Exception) {
            return array();
        }
    }

    public function latestSummary($limit = 25) {
        try {
            $products = \ORM::forTable('licenses')
                ->table_alias('t1')
                ->select('t1.*')
                ->select_expr('SUM(CASE WHEN t2.success = 1 THEN 1 ELSE 0 END)', 'auth_valid_count')
                ->select_expr('SUM(CASE WHEN t2.success = 0 THEN 1 ELSE 0 END)', 'auth_invalid_count')
                ->join('authorizations', array('t1.key', '=', 't2.license_key'), 't2')
                ->group_by('t1.id')
                ->limit($limit)
                ->order_by_desc('t1.id');
            
            return $products->findArray();
        }
        catch (\PDOException $Exception) {
            return array();
        }
    }

    public function total($search_phrase = "") {
        try {
            if (is_string($search_phrase) && strlen($search_phrase) > 0) {
                return \ORM::forTable('licenses')->where_like('customer_name', '%' . $search_phrase . '%')->count();
            }
            
            return \ORM::forTable('licenses')->count();
        }
        catch( \PDOException $Exception ) {
            return 0;
        }
    }
}