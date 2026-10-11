# CLI Response

`\WebServCo\Framework\Cli\Response`

Command methods return this. It holds an exit status and, optionally, some text to print.

- Implements: `Interfaces\ResponseInterface`

---

## Usage

It's the last line of a command method:

```php
use WebServCo\Framework\Cli\Response;

// content, exitStatus
return new Response('Hello World' . PHP_EOL, 0);
```

When something went wrong:

```php
return new Response('Error!' . PHP_EOL, 1);
```

If you already printed everything with `outputCli()`:

```php
// No content, exit status 0.
return new Response();
```

The application prints the content and exits with your status, which shell scripts and cron can check:

```shell
bin/cli Import/run && echo "ok"
```

---

## Notes

Older versions let you pass `true` or `false` as the status. With strict types, that's now a `TypeError`, so use an `int`.

An uncaught exception prints the [error message](../Application.md#error-output) and exits with `1`.
