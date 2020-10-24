<?php
namespace App\Utils;

class Reusable
{
    function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    public function getConstants($msg)
    {
        return $this->get('translator')->trans($msg,[],"messages");
    }
}