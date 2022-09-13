<?php

namespace WebServCo\Framework\Interfaces;

interface CliRunnerInterface
{
    public function finish();
    public function getPid();
    public function getStatistics();
    public function isRunning();
    public function start();
}
