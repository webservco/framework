<?php

declare(strict_types=1);

namespace WebServCo\Framework;

abstract class AbstractResponse
{

    protected string $content;

    protected int $statusCode;

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatus(): int
    {
        return $this->statusCode;
    }

    public function setContent(string $content): bool
    {
        $this->content = $content;
        return true;
    }
}
