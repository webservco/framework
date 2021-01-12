<?php
namespace WebServCo\Framework\Cli\Progress;

//https://gist.github.com/mayconbordin/2860547
final class Bar
{
    protected string $type;

    protected int $width;
    protected int $padding;
    protected int $total;
    protected int $item;

    protected string $outBar;
    protected string $outPad;
    protected string $outMessage;

    public function __construct(int $width = 20)
    {
        $this->type = 'single_line';
        $this->width = $width;
        $this->padding = 25;
        $this->total = 100;
        $this->item = 1;
    }

    public function start(int $total = 100) : bool
    {
        $this->total = $total;
        return true;
    }

    public function advanceTo(int $item) : bool
    {
        $this->item = $item;
        return true;
    }

    public function setType(string $type) : bool
    {
        if (!in_array($type, ['single_line', 'multi_line'])) {
            throw new \InvalidArgumentException('Invalid type.');
        }

        $this->type = $type;
        return true;
    }

    public function prefix(string $message = '') : string
    {
        switch ($this->type) {
            case 'single_line':
            case 'multi_line':
                return $this->prefixProgress($message);
            default:
                throw new \InvalidArgumentException('Invalid type.');
        }
    }

    public function suffix(bool $result = true) : string
    {
        switch ($this->type) {
            case 'single_line':
                return $this->suffixSingle($result);
            case 'multi_line':
                return $this->suffixMulti($result);
            default:
                throw new \InvalidArgumentException('Invalid type.');
        }
    }

    protected function prefixProgress(string $message) : string
    {
        $percent = round($this->item * 100 / $this->total);
        $bar = (int) round($this->width * $percent / 100);
        $this->outBar = sprintf(
            "%s%% [%s>%s] %s",
            $percent,
            str_repeat('=', $bar),
            str_repeat(' ', (int) round($this->width-$bar)),
            $this->item . '/' . $this->total
        );
        $this->outMessage = $message;

        $padLen = ($this->width + $this->padding) - strlen($this->outBar);
        $this->outPad = (0 < $padLen) ? str_repeat(' ', (int) $padLen) : '';
        return $this->outBar.$this->outPad.$this->outMessage;
    }

    protected function suffixSingle(bool $result, bool $overwrite = false) : string
    {
        $totalLen = strlen($this->outBar.$this->outPad.$this->outMessage);
        $output = null;

        if ($overwrite) {
            //overwrite current line
            $output .= "\033[" . $totalLen . 'D';
            $output .= str_repeat(' ', $this->width + $this->padding);
            $output .= $this->outMessage;
        }

        $padLen = 74 - $totalLen;
        if (0 < $padLen) {
            $output .= str_repeat(' ', $padLen);
        }
        $output .= '[';
        $output .= $result ? "\e[32mOK" : "\e[31mKO";
        $output .= "\e[0m" . ']';
        $output .= "\r";

        return $output;
    }

    protected function suffixMulti(bool $result, bool $overwrite = true) : string
    {
        $output = $this->suffixSingle($result, $overwrite);
        $output .= PHP_EOL;

        return $output;
    }

    public function finish() : string
    {
        return "\033[" . 0 . 'D' . str_repeat(' ', 74) . "\r";
    }
}
