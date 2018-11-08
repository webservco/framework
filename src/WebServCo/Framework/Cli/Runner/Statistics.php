<?php
namespace WebServCo\Framework\Cli\Runner;

final class Statistics
{
    protected $duration; // <seconds>.<microseconds>
    protected $memoryPeakUsage; // K
    protected $timeStart;
    protected $timeFinish;
    protected $timeZone;

    public function __construct()
    {
        $this->timeZone = date_default_timezone_get();
    }

    public function finish()
    {
        $this->timeFinish = $this->createCurrentTimeObject();
        $this->duration = $this->timeFinish->format("U.u") - $this->timeStart->format("U.u");
        $this->memoryPeakUsage = memory_get_peak_usage(true) / 1024;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function getMemoryPeakUsage()
    {
        return $this->memoryPeakUsage;
    }

    public function start()
    {
        $this->timeStart = $this->createCurrentTimeObject();
    }

    protected function createCurrentTimeObject()
    {
        $dateTime = \DateTime::createFromFormat('U.u', (string) microtime(true));
        if (!($dateTime instanceof \DateTime)) {
            throw new \WebServCo\Framework\Exceptions\ApplicationException(
                'Error initializing DateTime object'
            );
        }
        $dateTime->setTimezone(new \DateTimeZone($this->timeZone));
        return $dateTime;
    }
}
