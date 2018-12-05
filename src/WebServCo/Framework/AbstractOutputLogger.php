<?php
namespace WebServCo\Framework;

abstract class AbstractOutputLogger extends AbstractLogger
{
    public function output($string, $eol = true)
    {
        echo $string;
        if ($eol) {
            echo PHP_EOL;
        }
    }
}
