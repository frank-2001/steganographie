<?php
    function tobin($str){
        $str = (string)$str;
        $l = strlen($str);
        $result = '';

        while($l--){
            $result = str_pad(decbin(ord($str[$l])),8,"0",STR_PAD_LEFT).$result;
        }
        return $result;
    }

    function toString($bin){
        return pack("H*",base_convert($bin,2,16));
    }