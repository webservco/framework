<?php

namespace WebServCo\Framework\Cli;

final class Response extends \WebServCo\Framework\AbstractResponse implements
    \WebServCo\Framework\Interfaces\ResponseInterface
{
    public function __construct($content = null, $exitStatus = 0)
    {
        if (true === is_bool($exitStatus)) {
            $exitStatus = false === $exitStatus ? 1 : 0;
        }

        $this->setStatus($exitStatus);

        $this->setContent($content);
    }

    public function setStatus($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function send()
    {
        if (!empty($this->content)) {
            echo $this->content;
        }
        return $this->statusCode;
    }
}
