# ProgressBar

## \WebServCo\Framework\Cli\Progress\Bar

## Usage

### `__construct`:
```php
$this->progressBar = new \WebServCo\Framework\Cli\Progress\Bar(20); //size
$this->progressBar->setType('single_line'); //single_line, multi_line
```

### Before loop:
```php
$this->progressBar->start($totalData); // pb start
$i = 0; // pb counter
```

### During loop:
```php
++$i; // pb increment
$this->progressBar->advanceTo($i); // pb advance
$this->outputCli(
    $this->progressBar->prefix(sprintf('Processing %s/%s', $i, $totalData)),
    false
); // pb prefix

// processing
//$result = ... (bool)

$this->outputCli($this->progressBar->suffix($result), false); // pb suffix
```

### After loop:
```php
$this->progressBar->finish(); //pb finish
```
