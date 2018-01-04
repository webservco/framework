<?php
namespace WebServCo\Framework\Libraries;

class Date extends \WebServCo\Framework\Library
{
    const DEFAULT_TIMEZONE = 'Europe/Rome';
    
    public function setTimezone($timezoneIdentifier = null)
    {
        $timezoneIdentifier = $timezoneIdentifier ?:
            $this->setting('timezone', self::DEFAULT_TIMEZONE);
        
        if (in_array($timezoneIdentifier, \DateTimeZone::listIdentifiers())) {
            return date_default_timezone_set($timezoneIdentifier);
        }
        return false;
    }
    
    public function getTimezone()
    {
        return date_default_timezone_get();
    }
}
