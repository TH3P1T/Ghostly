<?php

/*
https://tls.mbed.org/kb/cryptography/asn1-key-structures-in-der-and-pem

-----BEGIN RSA PUBLIC KEY-----
BASE64 ENCODED DATA
-----END RSA PUBLIC KEY-----

RSAPublicKey ::= SEQUENCE {
    modulus           INTEGER,  -- n
    publicExponent    INTEGER   -- e
}

-----BEGIN RSA PRIVATE KEY-----
BASE64 ENCODED DATA
-----END RSA PRIVATE KEY-----

RSAPrivateKey ::= SEQUENCE {
  version           Version,
  modulus           INTEGER,  -- n
  publicExponent    INTEGER,  -- e
  privateExponent   INTEGER,  -- d
  prime1            INTEGER,  -- p
  prime2            INTEGER,  -- q
  exponent1         INTEGER,  -- d mod (p-1)
  exponent2         INTEGER,  -- d mod (q-1)
  coefficient       INTEGER,  -- (inverse of q) mod p
  otherPrimeInfos   OtherPrimeInfos OPTIONAL
}
*/

namespace Ghostly\Crypto;

class RSA {
    private $rsa_n;
    private $rsa_e;

    private $rsa_d;
    private $rsa_p;
    private $rsa_q;

    private $math_lib;

    public function __construct($key_data = "") {
        if (extension_loaded('gmp')) {
            $this->math_lib = new \Ghostly\Crypto\Math\GMP();
        }
        elseif (extension_loaded('bcmath')) {
            $this->math_lib = new \Ghostly\Crypto\Math\BCMath();
        }
        else {
            throw new \Exception('No math lib found (gmp or bcmath)');
        }

        if (strlen($key_data) > 0) {
            $key_sequence = \ASN1\Type\UnspecifiedType::fromDER(base64_decode($key_data))->asSequence();

            if ($key_sequence->has(5)) {
                $this->setPrivateKey($key_data);
            }
            elseif ($key_sequence->has(1)) {
                $this->setPublicKey($key_data);
            }
        }
    }

    public function setPublicKey($key_data) {
        if (strlen($key_data) <= 0) {
            return;
        }

        $key_sequence = \ASN1\Type\UnspecifiedType::fromDER(base64_decode($key_data))->asSequence();

        $this->rsa_n = $public_sequence->at(0)->asInteger()->number(); //modulus
        $this->rsa_e = $public_sequence->at(1)->asInteger()->number(); //public exponent
    }

    public function setPrivateKey($key_data) {
        if (strlen($key_data) <= 0) {
            return;
        }

        $key_sequence = \ASN1\Type\UnspecifiedType::fromDER(base64_decode($key_data))->asSequence();

        $this->rsa_n = $key_sequence->at(1)->asInteger()->number(); //modulus
        $this->rsa_e = $key_sequence->at(2)->asInteger()->number(); //public exponent
        $this->rsa_d = $key_sequence->at(3)->asInteger()->number(); //private exponent
        $this->rsa_p = $key_sequence->at(4)->asInteger()->number(); //P prime 1
        $this->rsa_q = $key_sequence->at(5)->asInteger()->number(); //Q prime 2
    }

    public function publicEncrypt($data) {
        return $this->encrypt($data, $this->rsa_e, $this->rsa_n);
    }

    public function privateEncrypt($data) {
        return $this->encrypt($data, $this->rsa_d, $this->rsa_n);
    }

    public function publicDecrypt($data) {
        return $this->decrypt($data, $this->rsa_e, $this->rsa_n);
    }

    public function privateDecrypt($data) {
        return $this->decrypt($data, $this->rsa_d, $this->rsa_n);
    }

    private function encrypt($data, $exponent, $modulus) {
		// divide plain data into chunks
		$data_len = strlen($data);
		$chunk_len = $this->math_lib->bitLen($this->rsa_n) - 1;
        $block_len = (int) ceil($chunk_len / 8);
        $chunk_len = $block_len - 2;
		$curr_pos = 0;
		$enc_data = '';
		while ($curr_pos < $data_len) 
		{
            $padding_value = 0;
            $chunk = substr($data, $curr_pos, $chunk_len);
            if (strlen($chunk) < $chunk_len)
            {
                $padding_value = $chunk_len - strlen($chunk);
                for ($i = 0; $i < $padding_value; $i++)
                {
                    $chunk .= chr($padding_value);
                }  
            }
            $chunk .= chr($padding_value);

			$tmp = $this->math_lib->bin2int($chunk);
			$enc_data .= strrev(str_pad($this->math_lib->int2bin($this->math_lib->powmod($tmp, $exponent, $modulus)), $block_len, "\0"));
			$curr_pos += $chunk_len;
		}
		return $enc_data;
    }

    private function decrypt($enc_data, $exponent, $modulus) {
		$data_len = strlen($enc_data);
		$chunk_len = $this->math_lib->bitLen($this->rsa_n) - 1;
        $block_len = (int) ceil($chunk_len / 8);
        $chunk_len = $block_len - 1;
		$curr_pos = 0;
		$bit_pos = 0;
		$plain_data = "";
		while ($curr_pos < $data_len) 
		{
			$tmp = $this->math_lib->bin2int(strrev(substr($enc_data, $curr_pos, $block_len)));
			$plain_data .= $this->math_lib->int2bin($this->math_lib->powmod($tmp, $exponent, $modulus));
			$curr_pos += $block_len;
		}
        
        $padding_index = ($data_len / $block_len) * ($chunk_len - 1);
        if (strlen($plain_data) > $padding_index)
        {
            $padding_value = ord($plain_data{$padding_index});
            $plain_data = substr($plain_data, 0, $padding_index - $padding_value);
        }

		return $plain_data;
    }
}