<?php
namespace WebServCo\Framework\Libraries;

final class CliResponse extends \WebServCo\Framework\AbstractResponse implements
    \WebServCo\Framework\Interfaces\ResponseInterface
{
    public function __construct($content = null, $status = 0)
    {
        $this->setContent($content);
        $this->status = $status; //TODO move to method
    }
}
