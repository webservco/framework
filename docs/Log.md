# Log

Namespace `\WebServCo\Framework\Log`

Three [PSR-3](https://www.php-fig.org/psr/psr-3/) loggers live here, one writing to a file and two printing to the screen. There's also an error processor for exceptions, and a trait for logging to a file and the screen in one call.

| Class | Implements | Writes to |
| --- | --- | --- |
| `FileLogger` | `Interfaces\FileLoggerInterface` | `<logDirectory><channel>.log` |
| `CliOutputLogger` | `Interfaces\OutputLoggerInterface` | the screen, and `clear()` clears the terminal |
| `OutputLogger` | `Interfaces\OutputLoggerInterface` | the screen |

Each one is a `\Psr\Log\LoggerInterface`, by way of `Interfaces\LoggerInterface`, which adds a `clear(): bool` method.

---

## FileLogger

```php
use WebServCo\Framework\Environment\Config;
use WebServCo\Framework\Log\FileLogger;

$logger = new FileLogger(
    // channel: the file name, without ".log"
    'import',
    // logDirectory: created if missing
    Config::string('APP_PATH_LOG'),
);

$logger->info('Import started.');
$logger->error('Item not found.', ['id' => 12]);
```

`var/log/import.log`:

```
[20241009.143012.123456] 127.0.0.1 info Import started.
[20241009.143012.234567] 127.0.0.1 error [context saved] Item not found.
```

A line starts with the timestamp, which doubles as the log id. After it come the remote address (in CLI, this machine's IP), the level and the message.

Context never goes in the line itself. It's dumped with `var_dump()` into a separate file named after the log id, `var/log/context-import/20241009.143012.234567.context` in this example.

```php
// Empty the log file. Returns false if the file could not be written.
$logger->clear();
// Number of the last line.
$lines = $logger->getLastLine();
// The log directory, with a trailing slash.
$directory = $logger->getLogDirectory();
```

An empty path, a path that isn't a directory or a directory PHP can't write to makes the constructor throw a `LoggerException`.

---

## Output loggers

```php
use WebServCo\Framework\Log\CliOutputLogger;

$outputLogger = new CliOutputLogger();
$outputLogger->info('Processing...');
// Print without a level.
$outputLogger->output('Done', true);
// Clear the terminal.
$outputLogger->clear();
```

Only the message reaches the screen. The level is dropped, and so is the context, though `CliOutputLogger` at least marks the line with `[context not outputted]` while `OutputLogger` says nothing.

Unknown levels throw a `\Psr\Log\InvalidArgumentException`. `LogLevel` has the valid ones, which match PSR-3.

---

## LogTrait

With `\WebServCo\Framework\Traits\LogTrait`, one call writes to the file logger and, when there is one, the output logger too. Your class needs two properties for it, `$fileLogger` (a `\Psr\Log\LoggerInterface`) and `$outputLogger` (a `?OutputLoggerInterface`).

```php
$this->logDebug('Details.', ['row' => $row]);
$this->logInfo('Item imported.');
$this->logWarning('Item skipped.');
$this->logError('Item failed.', ['id' => 12]);
```

[AbstractCliRunnerProcessor](Cli/Runner/Runner.md#processor) is built on it.

---

## Error processor

`\WebServCo\Framework\Processors\AbstractErrorProcessor` writes exceptions to `error.log` (HTTP) or `errorCLI.log` (CLI) in `APP_PATH_LOG`. It follows the `getPrevious()` chain all the way down and logs every exception in it, each with a trace in its context file.

What `report()` does is up to you. Logging plus an email is a common choice:

```php
namespace Project;

use Throwable;
use WebServCo\Framework\Processors\AbstractErrorProcessor;

final class ErrorProcessor extends AbstractErrorProcessor
{
    public function report(Throwable $exception, ?string $reference = null): bool
    {
        $this->logException($exception);

        // Send an email, notify a service, ...

        return true;
    }
}
```

Hook it into your [Application](Application.md#your-application) and every uncaught exception gets logged. It's just as useful for exceptions you catch and handle but still want on record:

```php
try {
    $client->send($order);
} catch (HttpClientException $e) {
    (new ErrorProcessor())->report($e);
    $this->setData('alert/message', __('The order could not be sent.'));
}
```

Because the constructor reads `APP_PATH_LOG`, create it only after the environment settings are loaded.

---

## Notes

Log messages name only the file (`Error: message in Controller:42.`). These messages sometimes reach users, and full server paths have no business there. For full paths, open the context file and look at the trace.

Nothing ever deletes the context files, so a busy site collects thousands. A cron job with `find var/log -name '*.context' -mtime +30 -delete` keeps them in check.
