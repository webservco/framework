<?php

declare(strict_types=1);

namespace WebServCo\Framework\Cli;

final class Response extends \WebServCo\Framework\AbstractResponse implements
    \WebServCo\Framework\Interfaces\ResponseInterface
{
    public function __construct(string $content = '', int $exitStatus = 0)
    {
        $this->setStatus($exitStatus);

        $this->setContent($content);
    }

    public function setStatus(int $statusCode): bool
    {
        $this->statusCode = $statusCode;
        return true;
    }

    public function send(): int
    {
        if (!empty($this->content)) {
            echo $this->content;
        }
        return $this->statusCode;
    }
}
