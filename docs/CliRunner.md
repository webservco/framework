# CliRunner

## \WebServCo\Framework\CliRunner

## Usage

### `__construct`:
```php
$this->cliRunner = new \WebServCo\Framework\CliRunner(
    sprintf('%svar/run/', $this->data('path/project'))
);
```

### Method start:
```php
$this->cliRunner->start(__METHOD__); // cli start
$this->outputCli(sprintf('pid: %s', $this->cliRunner->getPid(__METHOD__))); // cli pid
```

### During loop:
```php
if (!$this->cliRunner->hasPid(__METHOD__)) { // cli check
    $this->outputCli('Interrupt detected, stopping');
    break;
}
```
> in order to stop execution, simply remove the pid file from var/run

### Method end:
```php
return $this->cliRunner->finish(__METHOD__); // cli finish
```
> return will be false if interrupted, true otherwise
