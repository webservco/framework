<?php
namespace WebServCo\Framework;

final class CliResponse extends \WebServCo\Framework\AbstractResponse implements
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

    public function send(\WebServCo\Framework\Libraries\Request $request)
    {
        if (!empty($this->content)) {
            echo $this->content;
        }
        return $this->statusCode;
    }
}
