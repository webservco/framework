# Other helpers

Namespace `\WebServCo\Framework\Helpers`

---

## PhpHelper

```php
use WebServCo\Framework\Helpers\PhpHelper;

// true when running from the command line (PHP_SAPI === 'cli').
if (PhpHelper::isCli()) {
    // ...
}
```

---

## EnvironmentHelper

```php
use WebServCo\Framework\Helpers\EnvironmentHelper;
use WebServCo\Framework\Values\Environment;

// true, or throws an EnvironmentException.
EnvironmentHelper::validate(Environment::PRODUCTION);
```

Four values pass, the `\WebServCo\Framework\Values\Environment` constants `DEVELOPMENT`, `TESTING`, `STAGING` and `PRODUCTION`, which hold the same words in lower case. `APP_ENVIRONMENT` goes through this check when it's loaded.

```php
use WebServCo\Framework\Environment\Config;
use WebServCo\Framework\Values\Environment;

if (Config::string('APP_ENVIRONMENT') === Environment::DEVELOPMENT) {
    // Show debug information.
}
```

---

## TemplateHelper

For rendering a template where there's no controller (more in [HtmlOutput](../Output/HtmlOutput.md#rendering-outside-a-controller)).

```php
use WebServCo\Framework\Helpers\TemplateHelper;

$html = TemplateHelper::render($templatePath, 'emails/welcome', ['name' => 'Aria']);
```

---

## ErrorMessageHelper

```php
use WebServCo\Framework\Helpers\ErrorMessageHelper;

// "Error: <message> in <file name without .php>:<line>."
$message = ErrorMessageHelper::format($throwable);
```

The full path is left out on purpose, so the message is safe to show to users.

---

## ErrorObjectHelper

```php
use WebServCo\Framework\Helpers\ErrorObjectHelper;

// The exception if one is given, otherwise the last PHP error as an \ErrorException, or null.
$throwable = ErrorObjectHelper::get(null);
```

Reading the last error also clears it. One error gets special treatment. "POST Content-Length of X bytes exceeds the limit of Y bytes" comes back as an `UploadException` with code `Codes::INI_SIZE`, which [File upload](../Files/Upload.md#simple-upload) relies on.

---

## ErrorTypeHelper

```php
use WebServCo\Framework\Helpers\ErrorTypeHelper;

// "Warning"
$type = ErrorTypeHelper::getString(E_WARNING);
```

Turns a PHP error level into a name like `Warning`, `Notice`, `Deprecated`, `E_ERROR` or `E_USER_WARNING`. Anything it doesn't recognize is `Unknown`.

---

## Http\HeadersHelper

`\WebServCo\Framework\Helpers\Http\HeadersHelper` turns raw response headers into an array.

```php
use WebServCo\Framework\Helpers\Http\HeadersHelper;

$headers = HeadersHelper::parseString("HTTP/1.1 200 OK\nContent-Type: text/html\nSet-Cookie: a=1");
// ['content-type' => ['text/html'], 'set-cookie' => ['a=1']]

// One header per line. Keep the original case.
$headers = HeadersHelper::parseArray($lines, false);
```

It skips the status line and any line without `: `. Headers folded over several lines aren't supported.

There's one quirk with `$lowercaseKey = false`. Repeated `Set-Cookie` headers for the same cookie name collapse into the last one.
