# Ansi

`\WebServCo\Framework\Cli\Ansi`, `\WebServCo\Framework\Cli\Sgr`

ANSI escape codes for colors and styles in the terminal, and one to clear the screen.

---

## Usage

```php
use WebServCo\Framework\Cli\Ansi;
use WebServCo\Framework\Cli\Sgr;

// Clear the screen.
$this->outputCli(Ansi::clear(), false);

// Bold.
$this->outputCli(Ansi::sgr('Import', [Sgr::BOLD]));
// Red on yellow, underlined.
$this->outputCli(Ansi::sgr('Warning', [Sgr::RED, Sgr::BG_YELLOW, Sgr::UNDERLINE]));
```

You don't need to reset anything afterwards, because `sgr()` appends the reset code itself.

---

## Sgr constants

| Styles | Colors | Backgrounds |
| --- | --- | --- |
| `RESET` (0) | `BLACK` (30) | `BG_BLACK` (40) |
| `BOLD` (1) | `RED` (31) | `BG_RED` (41) |
| `FAINT` (2) | `GREEN` (32) | `BG_GREEN` (42) |
| `ITALIC` (3) | `YELLOW` (33) | `BG_YELLOW` (43) |
| `UNDERLINE` (4) | `BLUE` (34) | `BG_BLUE` (44) |
| `BLINK` (5) | `MAGENTA` (35) | `BG_MAGENTA` (45) |
| `REVERSE` (7) | `CYAN` (36) | `BG_CYAN` (46) |
| `CONCEAL` (8) | `WHITE` (37) | `BG_WHITE` (47) |
| `STRIKE` (9) | | |

Plain numbers work too. `90` gives you bright black, which most terminals show as gray.

---

## Notes

There's no check for a real terminal. Redirect the output to a file or a cron log and the codes show up there as junk like `\e[1m`.
