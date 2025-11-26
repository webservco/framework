<?php

declare(strict_types=1);

namespace WebServCo\Framework\Cli\Runner;

use DateTime;
use DateTimeZone;
use WebServCo\Framework\Exceptions\ApplicationException;

use function date_default_timezone_get;
use function floatval;
use function memory_get_peak_usage;
use function microtime;
use function sprintf;

final class Statistics
{
    // <seconds>.<microseconds>
    protected float $duration;

    // K
    protected int $memoryPeakUsage;
    protected bool $result;
    protected DateTime $timeStart;
    protected DateTime $timeFinish;
    protected string $timeZone;

    public function __construct()
    {
        $this->timeZone = date_default_timezone_get();
    }

    public function finish(bool $result): bool
    {
        $this->result = $result;
        $this->timeFinish = $this->createCurrentTimeObject();
        $this->duration = floatval($this->timeFinish->format("U.u")) - floatval($this->timeStart->format("U.u"));
        $this->memoryPeakUsage = memory_get_peak_usage(true) / 1024;

        return true;
    }

    public function getDuration(): float
    {
        return $this->duration;
    }

    public function getMemoryPeakUsage(): int
    {
        return $this->memoryPeakUsage;
    }

    public function getResult(): bool
    {
        return $this->result;
    }

    public function start(): bool
    {
        $this->timeStart = $this->createCurrentTimeObject();

        return true;
    }

    protected function createCurrentTimeObject(): DateTime
    {
        // https://www.php.net/manual/en/function.microtime.php#124984
        $microtime = sprintf('%.4f', microtime(true));
        $dateTime = DateTime::createFromFormat('U.u', $microtime);
        if (!($dateTime instanceof DateTime)) {
            throw new ApplicationException('Error initializing DateTime object:');
        }
        $dateTime->setTimezone(new DateTimeZone($this->timeZone));

        return $dateTime;
    }
}
