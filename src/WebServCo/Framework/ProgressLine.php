<?php
namespace WebServCo\Framework;

final class ProgressLine
{
    protected $padding;

    protected $outPad;
    protected $outMessage;

    protected $showResult;

    public function __construct()
    {
        $this->showResult = false;
        $this->padding = 0;
    }

    public function setShowResult($showResult)
    {
        $this->showResult = (bool) $showResult;
    }

    public function prefix($message = '')
    {
        $this->outMessage = $message;

        $this->outPad = (0 < $this->padding) ? str_repeat(' ', (int) $this->padding) : null;
        return $this->outPad.$this->outMessage;
    }

    public function suffix($result = true)
    {
        $totalLen = strlen($this->outPad.$this->outMessage);
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
            $output .= $result ? "\e[32mOK" : "\e[31mKO";
            $output .= "\e[0m" . ']';
        }

        $output .= "\r";

        return $output;
    }

    public function finish()
    {
        return "\033[" . 0 . 'D' . str_repeat(' ', 74) . "\r";
    }
}
