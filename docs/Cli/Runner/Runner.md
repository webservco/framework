# Runner

`\WebServCo\Framework\Cli\Runner\Runner`

An import that runs for an hour shouldn't need `kill -9` to stop. The runner writes a PID file at the start, your loop checks that the file is still there, and deleting it makes the command stop cleanly at the next check. Duration and peak memory are recorded along the way.

- Implements: `Interfaces\CliRunnerInterface`

---

## Usage

### Constructor

```php
use WebServCo\Framework\Cli\Runner\Runner;

// Directory for the PID file. Must exist, otherwise an ApplicationException is thrown.
$this->runner = new Runner(sprintf('%svar/run/', $projectPath));
```

### Start

```php
$this->runner->start();
// Full path of the PID file, for example /var/www/project/var/run/a1b2c3d4e5.pid
$this->outputCli(sprintf('pid: %s', $this->runner->getPid()));
```

### During the loop

```php
foreach ($items as $item) {
    if (!$this->runner->isRunning()) {
        $this->outputCli('Interrupt detected, stopping.');
        break;
    }
    // processing
}
```

Deleting the PID file from `var/run/` stops it.

### Finish

```php
// false if the command was interrupted (the PID file was already gone), true otherwise.
$result = $this->runner->finish();

// \WebServCo\Framework\Cli\Runner\Statistics
$statistics = $this->runner->getStatistics();
$this->outputCli(sprintf(
    'Result: %s. Duration: %.2fs. Peak memory: %dK.',
    $statistics->getResult() ? 'finished' : 'interrupted',
    $statistics->getDuration(),
    $statistics->getMemoryPeakUsage(),
));
```

`finish()` also removes the PID file, if it's still there.

---

## Processor

The work itself can go in a subclass of `\WebServCo\Framework\Processors\AbstractCliRunnerProcessor`. Its constructor takes the runner, an error processor and two loggers, and every log call writes to the file and the screen at once.

```php
use Throwable;
use WebServCo\Framework\Processors\AbstractCliRunnerProcessor;

final class ImportProcessor extends AbstractCliRunnerProcessor
{
    // \WebServCo\Framework\Interfaces\RunnerInterface
    public function run(): bool
    {
        $this->cliRunner->start();
        try {
            foreach ($this->getItems() as $item) {
                if (!$this->cliRunner->isRunning()) {
                    $this->logWarning('Interrupted.');
                    break;
                }
                $this->logInfo(sprintf('Item %s.', $item['id']));
            }
        } catch (Throwable $e) {
            $this->errorProcessor->logException($e);
            $this->logError($e->getMessage());
        }

        return $this->finish($this->cliRunner->finish());
    }

    protected function finish(bool $result): bool
    {
        $this->logInfo(sprintf('Done in %.2fs.', $this->cliRunner->getStatistics()->getDuration()));

        return $result;
    }
}
```

```php
use WebServCo\Framework\Cli\Runner\Runner;
use WebServCo\Framework\Environment\Config;
use WebServCo\Framework\Log\CliOutputLogger;
use WebServCo\Framework\Log\FileLogger;

$processor = new ImportProcessor(
    // \WebServCo\Framework\Interfaces\CliRunnerInterface
    new Runner(sprintf('%svar/run/', $projectPath)),
    // \WebServCo\Framework\Interfaces\ErrorProcessorInterface
    new ErrorProcessor(),
    // \Psr\Log\LoggerInterface
    new FileLogger('import', Config::string('APP_PATH_LOG')),
    // \WebServCo\Framework\Interfaces\OutputLoggerInterface, optional
    new CliOutputLogger(),
);
$result = $processor->run();
```

The `logDebug()`, `logInfo()`, `logWarning()` and `logError()` methods come from `Traits\LogTrait`, described in [Log](../../Log.md).
