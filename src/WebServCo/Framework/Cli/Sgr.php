<?php

namespace WebServCo\Framework\Cli;

/**
* Select Graphic Rendition
*/
final class Sgr
{
    public const RESET = 0;
    public const BOLD = 1;
    public const FAINT = 2;
    public const ITALIC = 3;
    public const UNDERLINE = 4;
    public const BLINK = 5;
    public const REVERSE = 7;
    public const CONCEAL = 8;
    public const STRIKE = 9;

    public const BLACK = 30;
    public const RED = 31;
    public const GREEN = 32;
    public const YELLOW = 33;
    public const BLUE = 34;
    public const MAGENTA = 35;
    public const CYAN = 36;
    public const WHITE = 37;

    public const BG_BLACK = 40;
    public const BG_RED = 41;
    public const BG_GREEN = 42;
    public const BG_YELLOW = 43;
    public const BG_BLUE = 44;
    public const BG_MAGENTA = 45;
    public const BG_CYAN = 46;
    public const BG_WHITE = 47;
}
