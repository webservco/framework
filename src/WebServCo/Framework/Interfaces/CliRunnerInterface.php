<?php

declare(strict_types=1);

namespace WebServCo\Framework\Interfaces;

interface CliRunnerInterface
{

    public function finish(): bool;

    public function getPid(): ?string;

    public function getStatistics(): \WebServCo\Framework\Cli\Runner\Statistics;

    public function isRunning(): bool;

    public function start(): bool;
}
