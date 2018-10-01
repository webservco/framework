<?php
namespace WebServCo\Framework;

//https://gist.github.com/mayconbordin/2860547
final class ProgressBar
{
    protected $type;

    protected $width;
    protected $padding;
    protected $total;
    protected $item;

    protected $outBar;
    protected $outPad;
    protected $outMessage;

    public function __construct($width = 20)
    {
        $this->type = 'single_line';
        $this->width = $width;
        $this->padding = 25;
        $this->total = 100;
        $this->item = 1;
    }

    public function start($total = 100)
    {
        $this->total = $total;
    }

    public function advanceTo($item)
    {
        $this->item = $item;
    }

    public function setType($type)
    {
        if (in_array($type, ['single_line', 'multi_line'])) {
            $this->type = $type;
        }
    }

    public function prefix($message = '')
    {
        switch ($this->type) {
            case 'single_line':
            case 'multi_line':
                return $this->prefixProgress($message);
                break;
        }
    }

    public function suffix($result = true)
    {
        switch ($this->type) {
            case 'single_line':
                return $this->suffixSingle($result);
                break;
            case 'multi_line':
                return $this->suffixMulti($result);
                break;
        }
    }

    protected function prefixProgress($message)
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
        $this->outPad = (0 < $padLen) ? str_repeat(' ', (int) $padLen) : null;
        return $this->outBar.$this->outPad.$this->outMessage;
    }

    protected function suffixSingle($result, $overwrite = false)
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

    protected function suffixMulti($result, $overwrite = true)
    {
        $output = $this->suffixSingle($result, $overwrite);
        $output .= PHP_EOL;

        return $output;
    }

    public function finish()
    {
        return "\033[" . 0 . 'D' . str_repeat(' ', 74) . "\r";
    }
}
