<?php
namespace WebServCo\Framework\Traits;

trait RequestUrlTrait
{
    public function guessAppUrl()
    {
        if (\WebServCo\Framework\Framework::isCLI()) {
            return false;
        }
        return $this->getSchema() .
        '://' .
        $this->getHost() .
        $this->path .
        DIRECTORY_SEPARATOR;
    }
}
