# ProgressLine

## `\WebServCo\Framework\Cli\Progress\Line`

Show a simple single line progress bar with a custom message.

## Usage

### `__construct`:
```php
$this->progressLine = new \WebServCo\Framework\Cli\Progress\Line();
# optional: don't show result
$this->progressLine->setShowResult(false);
```

### Before loop:
```php
$i = 0; // pl counter
```

### During loop:
```php
++$i; // pl increment
$this->outputCli(
    $this->progressLine->prefix(sprintf('Processing item %s', $i)),
    false
); // pl prefix

// processing
//$result = ... (bool)
$this->outputCli($this->progressLine->suffix($result), false); // pl suffix
```

### After loop:
```php
$this->progressLine->finish(); //pl finish
```
