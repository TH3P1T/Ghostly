<?php

namespace Ghostly\Crypto\Math;

class GMP {
    public function powmod($num, $pow, $mod)
    {
        return gmp_powm($num, $pow, $mod);
    }
    
    public function bin2int($str) {
        $result = 0;
        $n = strlen($str);
        do {
            // dirty hack: GMP returns FALSE, when second argument equals to int(0).
            // so, it must be converted to string '0'
            $result = gmp_add(gmp_mul($result, 256), strval(ord($str{--$n})));
        } while ($n > 0);
        return gmp_strval($result, 10);
    }

    public function int2bin($num) {
        $result = '';
        do {
            $result .= chr(gmp_intval(gmp_mod($num, 256)));
            $num = gmp_div($num, 256);
        } while (gmp_cmp($num, 0));
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