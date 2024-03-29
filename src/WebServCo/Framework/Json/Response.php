<?php

declare(strict_types=1);

namespace WebServCo\Framework\Json;

class Response extends \WebServCo\Framework\Http\Response
{
    public function __construct(\WebServCo\Framework\Interfaces\JsonInterface $jsonObject, int $statusCode = 200)
    {
        parent::__construct(
            $jsonObject->toJson(),
            $statusCode,
            ['Content-Type' => ['application/json']],
        );
    }
}
