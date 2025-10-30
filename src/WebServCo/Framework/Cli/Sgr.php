<?php

declare(strict_types=1);

namespace WebServCo\Framework\Cli;

/**
* Select Graphic Rendition
*/
final class Sgr
{
    public const int RESET = 0;
    public const int BOLD = 1;
    public const int FAINT = 2;
    public const int ITALIC = 3;
    public const int UNDERLINE = 4;
    public const int BLINK = 5;
    public const int REVERSE = 7;
    public const int CONCEAL = 8;
    public const int STRIKE = 9;

    public const int BLACK = 30;
    public const int RED = 31;
    public const int GREEN = 32;
    public const int YELLOW = 33;
    public const int BLUE = 34;
    public const int MAGENTA = 35;
    public const int CYAN = 36;
    public const int WHITE = 37;

    public const int BG_BLACK = 40;
    public const int BG_RED = 41;
    public const int BG_GREEN = 42;
    public const int BG_YELLOW = 43;
    public const int BG_BLUE = 44;
    public const int BG_MAGENTA = 45;
    public const int BG_CYAN = 46;
    public const int BG_WHITE = 47;
}
