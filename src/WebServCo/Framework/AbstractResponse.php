<?php
namespace WebServCo\Framework;

abstract class AbstractResponse
{
    protected $content;
    protected $statusCode;
    
    public function setContent($content)
    {
        $this->content = (string) $content;
    }
}
