<?php

declare(strict_types=1);

namespace WebServCo\Framework\Processors;

use WebServCo\Framework\Interfaces\CliRunnerInterface;
use WebServCo\Framework\Interfaces\ErrorProcessorInterface;
use WebServCo\Framework\Interfaces\LoggerInterface;
use WebServCo\Framework\Interfaces\OutputLoggerInterface;

abstract class AbstractCliRunnerProcessor implements \WebServCo\Framework\Interfaces\RunnerInterface
{
    // Log simple messages to both file and output.
    use \WebServCo\Framework\Traits\LogTrait;

    protected CliRunnerInterface $cliRunner;

    protected ErrorProcessorInterface $errorProcessor;

    protected LoggerInterface $fileLogger;

    protected ?OutputLoggerInterface $outputLogger = null;

    /**
    * Called by the "run" method.
    */
    abstract protected function finish(bool $result): bool;

    public function __construct(
        CliRunnerInterface $cliRunner,
        ErrorProcessorInterface $errorProcessor,
        LoggerInterface $fileLogger,
        ?OutputLoggerInterface $outputLogger = null
    ) {
        $this->cliRunner = $cliRunner;

        $this->errorProcessor = $errorProcessor;

        $this->fileLogger = $fileLogger;

        $this->outputLogger = $outputLogger;
    }
}
