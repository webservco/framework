<?php

declare(strict_types=1);

namespace WebServCo\Framework\Cli;

use WebServCo\Framework\AbstractResponse;
use WebServCo\Framework\Interfaces\ResponseInterface;

final class Response extends AbstractResponse implements
    ResponseInterface
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
        if ($this->content) {
            echo $this->content;
        }

        return $this->statusCode;
    }
}
