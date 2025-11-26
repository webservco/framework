<?php

declare(strict_types=1);

namespace WebServCo\Framework\Interfaces;

use WebServCo\Framework\Cli\Runner\Statistics;

interface CliRunnerInterface
{
    public function finish(): bool;

    public function getPid(): ?string;

    public function getStatistics(): Statistics;

    public function isRunning(): bool;

    public function start(): bool;
}
