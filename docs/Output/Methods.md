# Output methods

`\WebServCo\Framework\Traits\OutputTrait`

Every controller and repository has these. The HTML and JSON ones build an `Http\Response` for your action to return.

---

## `outputHtml(array $data, string $pageTemplate, ?string $mainTemplate = null): Response`

A full page, meaning the page template rendered inside the main template (the layout), sent as `text/html`.

```php
return $this->outputHtml($this->getData(), 'blog/post');
// Custom main template.
return $this->outputHtml($this->getData(), 'blog/post', 'layout-print');
// No page template: only the main template.
return $this->outputHtml($this->getData(), '', '404');
```

Templates and partials are explained in [HtmlOutput](HtmlOutput.md).

## `outputHtmlPartial(array $data, string $template): Response`

One template and no layout around it. Handy for AJAX.

```php
return $this->outputHtmlPartial($this->getData(), 'client/address');
```

## `outputJson(array $content, bool $result = true): Response`

Wraps your content as `{"data": <content>, "result": <result>}` and sends it as `application/json`.

```php
return $this->outputJson(['id' => 12]);
// {"data":{"id":12},"result":true}

return $this->outputJson(['message' => 'Invalid email.'], false);
// {"data":{"message":"Invalid email."},"result":false}
```

An API that expects a different shape needs [Json\Response](../Json.md) instead.

## `outputCli(string $string = '', bool $eol = true): bool`

Echoes the string and a `PHP_EOL` (skip the new line with `$eol = false`). Meant for commands, which still finish by returning a [Cli\Response](../Cli/Response.md).

```php
$this->outputCli('Processing...', false);
$this->outputCli(' done.');
```

## `setOutputCode(int $outputCode): bool`

Sets the HTTP status for every response created after the call. Until then it's 200.

```php
$this->setOutputCode(404);
return $this->outputHtml($this->getData(), 'errors/not-found');
```

## `output(): OutputLoaderInterface`

Gives you the output loader itself, for rendering to a string with no response involved:

```php
// Rendered template as a string.
$html = $this->output()->html($this->getData(), 'emails/welcome');
```

---

## Notes

There's one shared `HtmlOutput` and one shared `JsonOutput` per request, and each array you pass is merged into it. Data piles up that way, so a second `outputJson()` in the same request still carries the keys from the first.
