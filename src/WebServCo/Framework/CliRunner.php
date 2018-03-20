<?php
namespace WebServCo\Framework;

final class CliRunner
{
    protected $workDir;
    protected $script;
    protected $pid = [];

    public function __construct($workDir)
    {
        $this->workDir = $workDir;
    }

    public function start($script)
    {
        $this->pid[$script] = sprintf(
            '%s%s%s.pid',
            realpath($this->workDir),
            DIRECTORY_SEPARATOR,
            md5($script)
        );
        touch($this->pid[$script]);
        return true;
    }

    public function hasPid($script)
    {
        return is_readable($this->pid[$script]);
    }

    public function finish($script)
    {
        if (is_file($this->pid[$script]) &&
            is_readable($this->pid[$script])
        ) {
            unlink($this->pid[$script]);
            return true;
        }
        return false;
    }
}
