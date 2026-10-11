# HtmlOutput

`\WebServCo\Framework\Libraries\HtmlOutput`

Templates here are plain PHP files. Each one is included from inside an `HtmlOutput` object, which means `$this` in a template is that object and `$this->data()` reads whatever the controller passed in.

- Implements: `Interfaces\OutputInterface`

---

## Templates

They live in `resources/views/` and you refer to them without the `.php`, so `'blog/post'` means `resources/views/blog/post.php`. A full page has up to three parts:

| Template | Data key | Configured by |
| --- | --- | --- |
| partials (header, navigation, ...) | `tpl_<name>` | `partials` setting |
| page template | `tpl_content` | the controller |
| main template (layout) | | `main_template` setting, or the controller |

`resources/views/layout.php`:

```php
<!doctype html>
<html lang="<?=$this->data('lang', 'en')?>">
<head>
    <meta charset="utf-8">
    <title><?=htmlspecialchars($this->data('title', ''))?></title>
</head>
<body>
    <?=$this->data('tpl_header')?>
    <main><?=$this->data('tpl_content')?></main>
</body>
</html>
```

`resources/views/blog/post.php`:

```php
<h1><?=htmlspecialchars($this->data('post/title'))?></h1>
<p><?=__('Published on')?> <?=$this->data('post/date')?></p>
```

Partials render first, then the page template, and the main template last. A partial therefore can't see the page content, because it doesn't exist yet when the partial runs. The controller data is there for all of them.

---

## Configuration

`config/HtmlOutput.php`:

```php
return [
    // Main template. Default "layout".
    'main_template' => 'layout',
    // Partial templates rendered for every page. Default none.
    'partials' => ['header', 'navigation'],
];
```

---

## Returning HTML output

From a controller (all the variants are in [Output methods](Methods.md)):

```php
// Page template inside the main template.
return $this->outputHtml($this->getData(), 'blog/post');

// Custom main template.
return $this->outputHtml($this->getData(), 'blog/post', 'layout-print');

// Only the main template.
return $this->outputHtml($this->getData(), '', '404');

// Only one template, no main template.
return $this->outputHtmlPartial($this->getData(), 'client/address');

// Only one template, as a string.
$html = $this->output()->html($this->getData(), 'client/address');
```

---

## Rendering outside a controller

Emails and other places without a controller can use `\WebServCo\Framework\Helpers\TemplateHelper`, which renders with a fresh `HtmlOutput`:

```php
use WebServCo\Framework\Helpers\TemplateHelper;

$html = TemplateHelper::render(
    // templatePath, with a trailing slash
    sprintf('%sresources/views/', $projectPath),
    // templateName
    'emails/welcome',
    // data
    ['name' => 'Aria'],
);
```

---

## A template inside another template

Lists are the usual case, with one small template rendered per item in a `foreach`.

Cloning the current object keeps all the page data and adds the item:

```php
<?php foreach ($this->data('contacts/items', []) as $item) {
    $htmlOutput = clone $this;
    $htmlOutput->setData('contacts/item', $item);
    $htmlOutput->setTemplate('contacts/item');
    echo $htmlOutput->render();
} ?>
```

A new object starts empty, with just the item:

```php
<?php
$htmlOutput = new \WebServCo\Framework\Libraries\HtmlOutput();
// Same template directory as the current template.
$htmlOutput->setPath($this->path);
$htmlOutput->setData('contacts/item', $item);
$htmlOutput->setTemplate('contacts/item');
echo $htmlOutput->render();
```

`$this->path` is private. You can still read it here, since the template code runs inside `HtmlOutput`.

---

## Notes

Wrap anything a user typed in `htmlspecialchars()`. Templates don't escape output, and the request only sanitizes part of the input ([Request](../Libraries/Request.md#notes) says which part).

A template that doesn't exist throws an `ApplicationException`, so the visitor sees a 500. If a template throws halfway through, the half-rendered output is thrown away and the exception goes up unchanged.
