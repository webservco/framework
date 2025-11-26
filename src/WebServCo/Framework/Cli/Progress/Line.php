<?php

declare(strict_types=1);

namespace WebServCo\Framework\Cli\Progress;

use function str_repeat;
use function strlen;

final class Line
{
    protected int $padding;

    protected string $outPad;
    protected string $outMessage;

    protected bool $showResult;

    public function __construct()
    {
        $this->showResult = false;
        $this->padding = 0;
    }

    public function setShowResult(bool $showResult): bool
    {
        $this->showResult = $showResult;

        return true;
    }

    public function prefix(string $message = ''): string
    {
        $this->outMessage = $message;

        $this->outPad = 0 < $this->padding
            ? str_repeat(' ', (int) $this->padding)
            : '';

        return $this->outPad . $this->outMessage;
    }

    public function suffix(bool $result = true): string
    {
        $totalLen = strlen($this->outPad . $this->outMessage);
        $output = null;

        //overwrite current line
        $output .= "\033[" . $totalLen . 'D';
        $output .= str_repeat(' ', $this->padding);
        $output .= $this->outMessage;

        $padLen = 74 - $totalLen;
        if (0 < $padLen) {
            $output .= str_repeat(' ', $padLen);
        }
        if ($this->showResult) {
            $output .= '[';
            $output .= $result
                ? "\e[32mOK"
                : "\e[31mKO";
            $output .= "\e[0m" . ']';
        }

        $output .= "\r";

        return $output;
    }

    public function finish(): string
    {
        return "\033[" . 0 . 'D' . str_repeat(' ', 74) . "\r";
    }
}
