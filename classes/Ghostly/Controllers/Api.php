<?php

namespace Ghostly\Controllers;

class Api {
    protected $container;
    
    public function __construct($container) {
        $this->container = $container;
    }

    public function authorize($request, $response, $args) {
        $endpoints_service = $this->container->get('services')['endpoints'];
        $licenses_service = $this->container->get('services')['licenses'];
        $products_service = $this->container->get('services')['products'];
        
        $endpoint = $endpoints_service->getByName($args['endpoint']);
        $user_license = $request->getParam('license');
        $user_hardware_id = $request->getParam('hwid');
        $user_dh_key_data = $request->getParam('data');

        if ($endpoint == false) {
            return $this->respondAndLog($response, $this->errorResponse(-1000, "invalid endpoint"), $user_license, $user_hardware_id, false);
        }

        $license = $licenses_service->getByKey($request->getParam('license'));
        if ($license == false) {
            return $this->respondAndLog($response, $this->errorResponse(-1001, "invalid license"), $user_license, $user_hardware_id, false);
        }

        if ($license->disabled != 0) {
            return $this->respondAndLog($response, $this->errorResponse(-1002, "disabled license"), $license->key, $license->hardware_id, false);
        }

        $product = $products_service->getById($license->product_id);
        if ($product == false) {
            return $this->respondAndLog($response, $this->errorResponse(-1003, "invalid product"), $license->key, $license->hardware_id, false);
        }

        if (!is_string($user_hardware_id) || strlen($user_hardware_id) < 1) {
            return $this->respondAndLog($response, $this->errorResponse(-1004, "invalid hardware id"), $license->key, $user_hardware_id, false);
        }

        if (!is_string($license->hardware_id) || strlen($license->hardware_id) < 1) {
            $license->hardware_id = $user_hardware_id;
            $license->save();
        }

        if ($license->hardware_id != $user_hardware_id) {
            return $this->respondAndLog($response, $this->errorResponse(-1004, "invalid hardware id"), $license->key, $user_hardware_id, false);
        }

        $has_expiration_date = ($license->expiration_date != '0000-00-00 00:00:00' ? true : false);
        if ($has_expiration_date && new \DateTime("now", new \DateTimeZone("UTC")) >= new \DateTime($license->expiration_date, new \DateTimeZone("UTC")))
        {
            return $this->respondAndLog($response, $this->errorResponse(-1005, "license expired"), $license->key, $license->hardware_id, false);
        }
 
        $dh_key_exchange = new \Ghostly\Crypto\DHKeyExchange($user_dh_key_data);

        //generate authorization data
        $data = [ 
            'key' => $dh_key_exchange->getPublicKey(),
            'data' => ""
        ];

        //encode, encrypt, sign .. authorization data
        $packed_license_data = $this->binaryPackLicenseData($this->packLicenseData($license, $product));

        $rc4 = new \Ghostly\Crypto\RC4();

        //encrypt with rc4 (hwid)
        $rc4->setKey($license->hardware_id);
        $packed_license_data = $rc4->crypt($packed_license_data);

        //encrypt with rc4 (dh shared hash)
        $rc4->setKey($dh_key_exchange->getSharedSecretHash());
        $packed_license_data = $rc4->crypt($packed_license_data);

        //encrypt with rsa (product key)
        $product_rsa = new \Ghostly\Crypto\RSA($product->private_key);
        $packed_license_data = $product_rsa->privateEncrypt($packed_license_data);

        //encode
        $data['data'] = base64_encode($packed_license_data);

        return $this->respondAndLog($response, $data, $license->key, $license->hardware_id, true);
    }

    protected function binaryPackLicenseData($data) {
        $data_bin = "";
        
        //prepend random padding?
        $data_bin = random_bytes(13);

        //add each binary chunk
        foreach ($data as $entry) {
            if (is_string($entry)) {
                $data_bin .= $entry;
            }
            else {
                $data_bin = $data_bin . pack('C', $entry);
            }
        }  

        //add CRC ( first 4 bytes of hash)
        $data_bin_hash = sha1($data_bin, true);
        $data_bin .= chr(255);
        for ($i = 3; $i >= 0; $i--) {
            $data_bin .= $data_bin_hash[$i];
        }

        return $data_bin;
    }

    protected function packLicenseData($license, $product) {
        $data = array();

        //1 = license key
        $string_data = substr($license->key, 0, 255);

        $data[] = 1;
        $data[] = strlen($string_data);
        $data[] = $string_data;

        //2 = name
        if (is_string($license->customer_name) && strlen($license->customer_name) > 0) {
            $string_data = substr($license->customer_name, 0, 255);

            $data[] = 2;
            $data[] = strlen($string_data);
            $data[] = $string_data;
        }

        //3 = e-mail
        if (is_string($license->customer_email) && strlen($license->customer_email) > 0) {
            $string_data = substr($license->customer_email, 0, 255);

            $data[] = 3;
            $data[] = strlen($string_data);
            $data[] = $string_data;
        }

        //4 = hardware id
        if (is_string($license->hardware_id) && strlen($license->hardware_id) > 0) {
            $string_data = substr($license->hardware_id, 0, 255);

            $data[] = 4;
            $data[] = strlen($string_data);
            $data[] = $string_data;
        }

        //5 = expiration
        if ($license->expiration_date != '0000-00-00 00:00:00') {
            list($y, $m, $d) = explode('-', (new \DateTime($license->expiration_date, new \DateTimeZone("UTC")))->format('Y-m-d'));
            $y = intval($y);
            $m = intval($m);
            $d = intval($d);

            $data[] = 5;
            $data[] = $d;
            $data[] = $m;
            $data[] = $y % 256;
            $data[] = intval($y / 256);
        }
        else {
            $data[] = 5;
            $data[] = 0;
            $data[] = 0;
            $data[] = 0;
            $data[] = 0;
        }

        //6 = today
        list($y, $m, $d) = explode('-', (new \DateTime('now', new \DateTimeZone("UTC")))->format('Y-m-d'));
        $y = intval($y);
        $m = intval($m);
        $d = intval($d);

        $data[] = 6;
        $data[] = $d;
        $data[] = $m;
        $data[] = $y % 256;
        $data[] = intval($y / 256);

        //7 = product short name
        if (is_string($product->short_name) && strlen($product->short_name) > 0) {
            $string_data = substr($product->short_name, 0, 255);

            $data[] = 7;
            $data[] = strlen($string_data);
            $data[] = $string_data;
        }

        return $data;
    }

    protected function respondAndLog($response, $data, $license, $hardware_id, $success) {
        $authorizations_service = $this->container->get('services')['authorizations'];
        $authorizations_service->add($license, $hardware_id, $_SERVER['REMOTE_ADDR'], json_encode($data), ($success ? 1 : 0));

        return $response->withJson($data);
    }

    protected function errorResponse($code, $message) {
        $data = [
            'error' => [ 
                'code' => $code,  
                'message' => $message
            ]
        ];

        return $data;
    }
}