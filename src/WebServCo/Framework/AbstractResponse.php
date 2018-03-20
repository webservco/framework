<?php
namespace WebServCo\Framework;

abstract class AbstractResponse
{
    protected $content;
    protected $statusCode;

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getStatus()
    {
        return $this->statusCode;
    }
}
