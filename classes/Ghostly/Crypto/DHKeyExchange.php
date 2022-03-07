<?php

namespace Ghostly\Crypto;

class DHKeyExchange {
    protected $private_key;
    protected $their_sequence;
    protected $their_public_key;

    private $math_lib;

    public function __construct($der_public_key) {
        if (extension_loaded('gmp')) {
            $this->math_lib = new \Ghostly\Crypto\Math\GMP();
        }
        elseif (extension_loaded('bcmath')) {
            $this->math_lib = new \Ghostly\Crypto\Math\BCMath();
        }
        else {
            throw new \Exception('No math lib found (gmp or bcmath)');
        }

        $this->their_sequence = \ASN1\Type\UnspecifiedType::fromDER(base64_decode($der_public_key))->asSequence();
        $prime = $this->their_sequence->at(2)->asInteger()->number();
        $generator = $this->their_sequence->at(3)->asInteger()->number();
        $this->their_public_key = $this->their_sequence->at(4)->asInteger()->number();

        $config = array();
        $config['p'] = strrev($this->math_lib->int2bin($prime));
        $config['g'] = strrev($this->math_lib->int2bin($generator));
        $this->private_key = openssl_pkey_new(array('dh' => $config));

        if ($this->private_key == false) {
            throw new \RuntimeException("failed loading public key data");
        }
    }

    public function getPublicKey() {
        $details = openssl_pkey_get_details($this->private_key);

        $local_public_key = $details['dh']['pub_key'];

        $pub_seq = $this->their_sequence->withReplaced(4, 
            new \ASN1\Type\Primitive\Integer($this->math_lib->bin2int(strrev($local_public_key))));

        return base64_encode($pub_seq->toDER());
    }

    public function getSharedSecretHash() {
        $shared_secret = openssl_dh_compute_key(strrev($this->math_lib->int2bin($this->their_public_key)), $this->private_key);

        return sha1($shared_secret);
    }
}