<?php
namespace WebServCo\Framework\Cli\Runner;

final class Runner implements \WebServCo\Framework\Interfaces\CliRunnerInterface
{
    protected $pid;
    protected $statistics;
    protected $workDir;

    public function __construct($workDir)
    {
        if (!is_readable($workDir)) {
            throw new \WebServCo\Framework\Exceptions\ApplicationException('Working directory not readable');
        }

        $this->statistics = new Statistics();
        $this->workDir = $workDir;
    }

    public function finish()
    {
        $this->statistics->finish();
        if (empty($this->pid) || !is_file($this->pid) || !is_readable($this->pid)) {
            return false;
        }
        unlink($this->pid);
        $this->pid = null;
        return true;
    }

    public function getPid()
    {
        if (!$this->isRunning()) {
            return false;
        }
        return $this->pid;
    }

    public function getStatistics()
    {
        return $this->statistics;
    }

    public function isRunning()
    {
        if (empty($this->pid)) {
            return false;
        }
        return is_readable($this->pid);
    }

    public function start()
    {
        $this->statistics->start();
        $this->pid = sprintf(
            '%s%s%s.pid',
            realpath($this->workDir),
            DIRECTORY_SEPARATOR,
            microtime(true)
        );
        touch($this->pid);
        return true;
    }
}
