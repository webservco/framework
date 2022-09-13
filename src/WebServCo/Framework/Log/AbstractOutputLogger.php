<?php

namespace WebServCo\Framework\Log;

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
