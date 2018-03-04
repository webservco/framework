<?php
namespace WebServCo\Framework\Libraries;

final class Security extends \WebServCo\Framework\AbstractLibrary
{
    public function getSalt($string, $saltLength = 20)
    {
        return strrev(substr(sha1(strtolower($string)), 1, $saltLength));
    }
    
    public function getHash($string, $salt)
    {
        return $salt . sha1($salt . $string);
    }
}
