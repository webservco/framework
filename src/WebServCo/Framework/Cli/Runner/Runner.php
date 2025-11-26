<?php

declare(strict_types=1);

namespace WebServCo\Framework\Cli\Runner;

use WebServCo\Framework\Exceptions\ApplicationException;
use WebServCo\Framework\Interfaces\CliRunnerInterface;

use function bin2hex;
use function is_file;
use function is_readable;
use function random_bytes;
use function realpath;
use function sprintf;
use function touch;
use function unlink;

use const DIRECTORY_SEPARATOR;

final class Runner implements CliRunnerInterface
{
    protected string $pid;
    protected Statistics $statistics;

    public function __construct(protected string $workDir)
    {
        if (!is_readable($workDir)) {
            throw new ApplicationException('Working directory not readable.');
        }

        $this->statistics = new Statistics();
    }

    public function finish(): bool
    {
        if (!$this->pid || !is_file($this->pid) || !is_readable($this->pid)) {
            $result = false;
        } else {
            unlink($this->pid);
            $this->pid = '';
            $result = true;
        }
        $this->statistics->finish($result);

        return $result;
    }

    public function getPid(): ?string
    {
        if (!$this->isRunning()) {
            return null;
        }

        return $this->pid;
    }

    public function getStatistics(): Statistics
    {
        return $this->statistics;
    }

    public function isRunning(): bool
    {
        if (!$this->pid) {
            return false;
        }

        return is_readable($this->pid);
    }

    public function start(): bool
    {
        $this->statistics->start();
        $this->pid = sprintf(
            '%s%s%s.pid',
            realpath($this->workDir),
            DIRECTORY_SEPARATOR,
            bin2hex(random_bytes(5)),
        );
        touch($this->pid);

        return true;
    }
}
