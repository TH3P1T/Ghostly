<?php

namespace Ghostly\Crypto;

class RC4 {
    protected $key_state;
    protected $key_state_indices;

    public function __construct($key = "") {
        $this->setKey($key);
    }

    public function setKey($key = "") {
        $this->key_state_indices = array(0, 0);
        $this->key_state = range(0, 255);

        $key_length = strlen($key);
        if ($key_length <= 0) {
            return;
        }

        $j = 0;
        for ($i = 0; $i < 256; $i++) {
            $j = ($j + $this->key_state[$i] + ord($key[$i % $key_length])) & 255;
            $temp = $this->key_state[$i];
            $this->key_state[$i] = $this->key_state[$j];
            $this->key_state[$j] = $temp;
        }
    }

    public function crypt($text) {
        $i = &$this->key_state_indices[0];
        $j = &$this->key_state_indices[1];

        $len = strlen($text);
        for ($k = 0; $k < $len; ++$k) {
            $i = ($i + 1) & 255;
            $ksi = $this->key_state[$i];
            $j = ($j + $ksi) & 255;
            $ksj = $this->key_state[$j];

            $this->key_state[$i] = $ksj;
            $this->key_state[$j] = $ksi;
            $text[$k] = $text[$k] ^ chr($this->key_state[($ksj + $ksi) & 255]);
        }

        return $text;
    }
}