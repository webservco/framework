<?php

declare(strict_types=1);

namespace WebServCo\Framework\Processors;

use Psr\Log\LoggerInterface;
use WebServCo\Framework\Interfaces\CliRunnerInterface;
use WebServCo\Framework\Interfaces\ErrorProcessorInterface;
use WebServCo\Framework\Interfaces\OutputLoggerInterface;
use WebServCo\Framework\Interfaces\RunnerInterface;
use WebServCo\Framework\Traits\LogTrait;

abstract class AbstractCliRunnerProcessor implements RunnerInterface
{
    // Log simple messages to both file and output.
    use LogTrait;

    /**
    * Called by the "run" method.
    */
    abstract protected function finish(bool $result): bool;

    public function __construct(
        protected CliRunnerInterface $cliRunner,
        protected ErrorProcessorInterface $errorProcessor,
        protected LoggerInterface $fileLogger,
        protected ?OutputLoggerInterface $outputLogger = null,
    ) {
    }
}
