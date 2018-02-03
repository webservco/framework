<?php
namespace WebServCo\Framework\Libraries;

final class CliResponse extends \WebServCo\Framework\AbstractResponse implements
    \WebServCo\Framework\Interfaces\ResponseInterface
{
    public function __construct($content = null, $statusCode = 0)
    {
        $this->setStatus($statusCode);
        
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
