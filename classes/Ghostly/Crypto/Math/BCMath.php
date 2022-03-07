<?php

namespace Ghostly\Crypto\Math;

class BCMath {
    public function powmod($num, $pow, $mod)
    {
        if (function_exists('bcpowmod')) {
            return bcpowmod($num, $pow, $mod);
        }

        $result = '1';
        do {
            if (!bccomp(bcmod($pow, '2'), '1')) {
                $result = bcmod(bcmul($result, $num), $mod);
            }
            $num = bcmod(bcpow($num, '2'), $mod);
            $pow = bcdiv($pow, '2');
        } while (bccomp($pow, '0'));
        return $result;
    }
    
    public function bin2int($str) {
        $result = '0';
        $n = strlen($str);
        do {
            $result = bcadd(bcmul($result, '256'), ord($str{--$n}));
        } while ($n > 0);
        return $result;
    }

    public function int2bin($num) {
        $result = '';
        do {
            $result .= chr(bcmod($num, '256'));
            $num = bcdiv($num, '256');
        } while (bccomp($num, '0'));
        return $result;
    }

    public function bitLen($num) {
        $tmp = $this->int2bin($num);
        $bit_len = strlen($tmp) * 8;
        $tmp = ord($tmp{strlen($tmp) - 1});
        if (!$tmp) {
            $bit_len -= 8;
        }
        else {
            while (!($tmp & 0x80)) {
                $bit_len--;
                $tmp <<= 1;
            }
        }
        return $bit_len;
    }
}