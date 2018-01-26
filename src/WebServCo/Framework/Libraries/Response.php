<?php
namespace WebServCo\Framework\Libraries;

final class Response extends \WebServCo\Framework\AbstractLibrary
{
    public function formatStatusHeaderText($statusCode)
    {
        if ($statusMessage =
         \WebServCo\Framework\Http::getStatusCodeMessage($statusCode)) {
            return "HTTP/1.1 {$statusCode} {$statusMessage}";
        }
        return false;
    }
    
    public function setStatusHeader($statusCode)
    {
        if ($statusHeaderText = $this->formatStatusHeaderText($statusCode)) {
            header($statusHeaderText);
            return true;
        }
        return false;
    }
}
