<?php
namespace WebServCo\Framework;

abstract class AbstractResponse
{
    protected $content;
    protected $status;
    
    public function setContent($content)
    {
        $this->content = (string) $content;
    }
}
