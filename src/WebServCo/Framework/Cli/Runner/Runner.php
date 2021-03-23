<?php

declare(strict_types=1);

namespace WebServCo\Framework\Cli\Runner;

final class Runner implements \WebServCo\Framework\Interfaces\CliRunnerInterface
{

    protected string $pid;
    protected Statistics $statistics;
    protected string $workDir;

    public function __construct(string $workDir)
    {
        if (!\is_readable($workDir)) {
            throw new \WebServCo\Framework\Exceptions\ApplicationException('Working directory not readable.');
        }

        $this->statistics = new Statistics();
        $this->workDir = $workDir;
    }

    public function finish(): bool
    {
        if (empty($this->pid) || !\is_file($this->pid) || !\is_readable($this->pid)) {
            $result = false;
        } else {
            \unlink($this->pid);
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
        if (empty($this->pid)) {
            return false;
        }
        return \is_readable($this->pid);
    }

    public function start(): bool
    {
        $this->statistics->start();
        $this->pid = \sprintf(
            '%s%s%s.pid',
            \realpath($this->workDir),
            \DIRECTORY_SEPARATOR,
            \bin2hex(\random_bytes(5)),
        );
        \touch($this->pid);
        return true;
    }
}
