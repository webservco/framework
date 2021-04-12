# Runner

## `\WebServCo\Framework\Cli\Runner\Runner`

## Usage

### `__construct`:
```php
$this->runner = new \WebServCo\Framework\Cli\Runner\Runner(
    sprintf('%svar/run/', $projectPath)
);
```

### Method start:
```php
$this->runner->start(); // cli start
$this->outputCli(sprintf('pid: %s', $this->runner->getPid())); // cli pid
```

### During loop:
```php
if (!$this->runner->isRunning()) { // cli check
    $this->outputCli('Interrupt detected, stopping');
    break;
}
```
> in order to stop execution, simply remove the pid file from var/run

### Method end:
```php
$this->runner->finish(); // cli finish
$statistics = $this->runner->getStatistics();
```
> return will be false if interrupted, true otherwise
