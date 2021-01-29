<?php

declare(strict_types=1);

namespace WebServCo\Framework\Cli;

/**
* Select Graphic Rendition
*/
final class Sgr
{
    const RESET = 0;
    const BOLD = 1;
    const FAINT = 2;
    const ITALIC = 3;
    const UNDERLINE = 4;
    const BLINK = 5;
    const REVERSE = 7;
    const CONCEAL = 8;
    const STRIKE = 9;

    const BLACK = 30;
    const RED = 31;
    const GREEN = 32;
    const YELLOW = 33;
    const BLUE = 34;
    const MAGENTA = 35;
    const CYAN = 36;
    const WHITE = 37;

    const BG_BLACK = 40;
    const BG_RED = 41;
    const BG_GREEN = 42;
    const BG_YELLOW = 43;
    const BG_BLUE = 44;
    const BG_MAGENTA = 45;
    const BG_CYAN = 46;
    const BG_WHITE = 47;
}
