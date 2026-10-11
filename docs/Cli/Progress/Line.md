# Progress\Line

`\WebServCo\Framework\Cli\Progress\Line`

One line that gets rewritten for every item, with your message and, if you want it, an OK or KO result. Use it when a [Bar](Bar.md) would be too much.

---

## Usage

Like the bar, it returns strings and leaves the printing to you (`outputCli($string, false)`).

### Constructor

```php
use WebServCo\Framework\Cli\Progress\Line;

$this->progressLine = new Line();
// Optional: show [OK] / [KO] after the message. Default false.
$this->progressLine->setShowResult(true);
```

### Before the loop

```php
$i = 0;
```

### During the loop

```php
foreach ($items as $item) {
    ++$i;
    $this->outputCli($this->progressLine->prefix(sprintf('Processing item %s', $i)), false);

    // processing
    $result = $this->process($item); // bool

    $this->outputCli($this->progressLine->suffix($result), false);
}
```

### After the loop

```php
$this->outputCli($this->progressLine->finish(), false);
```

What `finish()` returns blanks out the current line.
