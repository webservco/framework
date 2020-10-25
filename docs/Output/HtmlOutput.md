# `\WebServCo\Framework\Libraries\HtmlOutput`

## Helper

`WebServCo\Framework\Utils\Template`

```php
$html = \WebServCo\Framework\Utils\Template::render(
    $templatePath,
    $templateName,
    $data
);
echo $html;
```

---

## Using a partial template inside another template.

* Use inside template code;
* Eg. using an individual item template in a `foreach` loop;

### Making a copy of the current object with all the data and adding item data

```php
$htmlOutput = clone $this; // \WebServCo\Framework\Libraries\HtmlOutput
$htmlOutput->setData('contacts/items/item', $item); // add item data
$htmlOutput->setTemplate('contacts/items/item');
echo $htmlOutput->render(); // output
```

### Creating a new object with only the item data

```php
$htmlOutput = new \WebServCo\Framework\Libraries\HtmlOutput();
$htmlOutput->setPath($this->path);
$htmlOutput->setData('contacts/items/item', $item); // add item data
$htmlOutput->setTemplate('contacts/items/item');
echo $htmlOutput->render(); // output
```

---
