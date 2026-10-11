# RequestHelper

`\WebServCo\Framework\Helpers\RequestHelper`

The [Request library](../Libraries/Request.md) uses these to pick the URL apart and clean up values. Two of them earn a place in your own code too, `sanitizeString()` and `getRemoteAddress()`.

---

## Usage

```php
use WebServCo\Framework\Helpers\RequestHelper;

$result = RequestHelper::method($arguments);
```

---

## Static methods

### `sanitizeString()`

`sanitizeString(string $string, bool $removeNewlines = true): string`

Strips tags, null bytes, backticks, backslashes, `$`, backspaces and tabs. New lines go too, unless `$removeNewlines` is `false`. Quotes get encoded, `'` as `&#39;` and `"` as `&#34;`. It stands in for `FILTER_SANITIZE_STRING`, deprecated since PHP 8.1.

```php
RequestHelper::sanitizeString("<b>O'Neil</b> paid $5");
// O&#39;Neil paid 5

// Keep the lines of a textarea.
RequestHelper::sanitizeString($text, false);
```

The framework runs it over `$_SERVER` values, the target, the query, POST keys, CLI arguments and cookie values.

### `getRemoteAddress(): string`

Gives the sanitized `REMOTE_ADDR`, this machine's IP in CLI, or `''` when neither is available. It matches `$this->request()->getRemoteAddress()` but works without loading the library.

### URL parsing

The rest split the request URI into target, query and suffix. You'll rarely call them directly:

| Method | Does |
| --- | --- |
| `parse(string $string, string $path, string $filename, array $suffixes): array` | removes the base path and script name, then returns `[target, query, suffix]` |
| `explode(string $string): array` | splits at the first `?` (or `&`): `[path, query]` |
| `removeSuffix(string $string, array $suffixes = []): array` | `['blog/post', '.html']` for `'blog/post.html'` |
| `transform(string $string): string` | `a=1&b=2` to `a/1/b/2`, trims `/` |
| `split(string $string): array` | splits on `/`, URL decodes, drops empty parts |
| `format(string $string): array` | `'a/1/b/2'` to `['a' => '1', 'b' => '2']` |
