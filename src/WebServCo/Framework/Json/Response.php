<?php

declare(strict_types=1);

namespace WebServCo\Framework\Json;

use WebServCo\Framework\Http\Response as HttpResponse;
use WebServCo\Framework\Interfaces\JsonInterface;

final class Response extends HttpResponse
{
    public function __construct(JsonInterface $jsonObject, int $statusCode = 200)
    {
        parent::__construct(
            $jsonObject->toJson(),
            $statusCode,
            ['Content-Type' => ['application/json']],
        );
    }
}
