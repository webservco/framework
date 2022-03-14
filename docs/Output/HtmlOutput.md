# `\WebServCo\Framework\Libraries\HtmlOutput`

## Returning HTML output

In controller:

### Default `pageTemplate`, default `mainTemplate`

* Returns `\WebServCo\Framework\Http\Response`

```php
return $this->outputHtml($this->getData(), $this->getView(__FUNCTION__));
```

### Custom `mainTemplate`

* Returns `\WebServCo\Framework\Http\Response`

```php
return $this->outputHtml($this->getData(), $this->getView(__FUNCTION__), 'customMainTemplate');
```

### `pageTemplate` is optional

* Returns `\WebServCo\Framework\Http\Response`

```php
return $this->outputHtml($this->getData(), '', '404');
```

### Partial template (no main template)

* Returns `\WebServCo\Framework\Http\Response`

```php
return $this->outputHtmlPartial($this->getData(), 'client/address');
```

### Partial template (no main template): string only

* Returns string
* Use case: Using a partial template inside another template (set in controller)

```php
return $this->output()->html($this->getData(), 'client/address');
```

---

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

## Using a partial template inside another template (set in template).

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
