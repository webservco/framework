<?php

namespace WebServCo\Framework\Cli;

final class Ansi
{
    /**
    * Select Graphic Rendition
    *
    * @see \WebServCo\Framework\Cli\Sgr
    *
    * @param string $string
    * @param array $parameters ANSI SGR parameters (alterantively use the Sgr class constants)
    *
    * @return string
    */
    public static function sgr($string, array $parameters)
    {
        $result = '';
        foreach ($parameters as $parameter) {
            $result .= sprintf("\e[%sm", $parameter);
        }
        $result .= $string . sprintf("\e[%sm", Sgr::RESET);
        return $result;
    }

    public static function clear()
    {
        return "\e[H\e[J";
    }
}
