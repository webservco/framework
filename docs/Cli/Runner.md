# Runner

## \WebServCo\Framework\Cli\Runner

## Usage

### `__construct`:
```php
$this->runner = new \WebServCo\Framework\Cli\Runner(
    sprintf('%svar/run/', $this->data('path/project'))
);
```

### Method start:
```php
$this->runner->start(__METHOD__); // cli start
$this->outputCli(sprintf('pid: %s', $this->runner->getPid(__METHOD__))); // cli pid
```

### During loop:
```php
if (!$this->runner->hasPid(__METHOD__)) { // cli check
    $this->outputCli('Interrupt detected, stopping');
    break;
}
```
> in order to stop execution, simply remove the pid file from var/run

### Method end:
```php
return $this->runner->finish(__METHOD__); // cli finish
```
> return will be false if interrupted, true otherwise
