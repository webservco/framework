<?php
namespace WebServCo\Framework\Json;

class Response extends \WebServCo\Framework\Http\Response
{
    public function __construct(
        \WebServCo\Framework\Interfaces\JsonInterface $jsonObject
    ) {
        parent::__construct(
            $jsonObject->toJson(),
            200,
            ['Content-Type' => 'application/json']
        );
    }
}
