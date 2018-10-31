<?php
namespace WebServCo\Framework\Cli;

final class Runner
{
    protected $workDir;
    protected $token;
    protected $pids = [];

    public function __construct($workDir)
    {
        $this->workDir = $workDir;
    }

    public function start($token)
    {
        $this->pids[$token] = sprintf(
            '%s%s%s.pid',
            realpath($this->workDir),
            DIRECTORY_SEPARATOR,
            md5($token)
        );
        touch($this->pids[$token]);
        return true;
    }

    public function hasPid($token)
    {
        return is_readable($this->pids[$token]);
    }

    public function getPid($token)
    {
        if (!$this->hasPid($token)) {
            return false;
        }
        return $this->pids[$token];
    }

    public function finish($token)
    {
        if (is_file($this->pids[$token]) &&
            is_readable($this->pids[$token])
        ) {
            unlink($this->pids[$token]);
            return true;
        }
        return false;
    }
}
