# Progress\Bar

`\WebServCo\Framework\Cli\Progress\Bar`

Shows how far a loop has got, with a message and an OK or KO result per item:

```
45% [=========>           ] 9/20   Processing 9/20                    [OK]
```

---

## Usage

The bar never prints anything itself. Methods that produce output return a string, and you print it with `outputCli($string, false)`.

### Constructor

```php
use WebServCo\Framework\Cli\Progress\Bar;

// Bar width in characters. Default 20.
$this->progressBar = new Bar(20);
// "single_line" (default): each item overwrites the previous one.
// "multi_line": one line per item.
$this->progressBar->setType('single_line');
```

Any other type throws an `\InvalidArgumentException`.

### Before the loop

```php
// Total number of items. Default 100.
$this->progressBar->start(count($items));
$i = 0;
```

### During the loop

```php
foreach ($items as $item) {
    ++$i;
    $this->progressBar->advanceTo($i);
    $this->outputCli(
        $this->progressBar->prefix(sprintf('Processing %s/%s', $i, count($items))),
        false,
    );

    // processing
    $result = $this->process($item); // bool

    $this->outputCli($this->progressBar->suffix($result), false);
}
```

### After the loop

```php
$this->outputCli($this->progressBar->finish(), false);
```

What `finish()` returns blanks out the current line.

---

## Notes

With nothing to process, skip the bar. `start(0)` leads to a division by zero in `prefix()`.

Lines are padded to 74 characters. A long message pushes the result past that.
