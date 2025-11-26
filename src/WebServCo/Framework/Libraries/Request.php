<?php

declare(strict_types=1);

namespace WebServCo\Framework\Libraries;

use WebServCo\Framework\AbstractRequest;
use WebServCo\Framework\Interfaces\RequestInterface;
use WebServCo\Framework\Traits\RequestProcessTrait;
use WebServCo\Framework\Traits\RequestServerTrait;
use WebServCo\Framework\Traits\RequestUrlTrait;

final class Request extends AbstractRequest implements
    RequestInterface
{
    use RequestProcessTrait;
    use RequestServerTrait;
    use RequestUrlTrait;

    /**
    * @param array<string,string|array<mixed>> $settings
    * @param array<string,mixed> $server
    * @param array<string,string> $post
    */
    public function __construct(array $settings, array $server, array $post = [])
    {
        parent::__construct($settings);

        $this->init($server, $post);
    }

    /**
    * @return array<int,string>
    */
    public function getArgs(): array
    {
        return $this->args;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
