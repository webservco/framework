# Application

`\WebServCo\Framework\AbstractApplication`, `\WebServCo\Framework\Application`

One request, from a browser or from the command line. The application loads the configuration, works out which controller (or command) method should answer, sends whatever that method returns, and falls back to an error page when something throws along the way.

---

## Project layout

```
bin/cli                                  CLI entry point
config/.env.ini                          environment settings
config/<Library>.php                     library settings (Router.php, Session.php, ...)
public/index.php                         web entry point (document root)
resources/translations/                  gettext .mo files
resources/views/                         PHP templates
src/Project/App.php                      your Application
src/Project/OutputLoader.php             your OutputLoader
src/Project/Domain/<Name>/Controller.php HTTP handlers
src/Project/Domain/<Name>/Command.php    CLI handlers
var/log/                                 log files (APP_PATH_LOG)
```

Unless you pass another one, the namespace is `Project`. Map it in `composer.json`:

```json
"autoload": {
    "psr-4": {
        "Project\\": "src/Project/"
    }
}
```

---

## Entry points

### Web: `public/index.php`

```php
use Project\App;
use WebServCo\Framework\Exceptions\ApplicationException;

require __DIR__ . '/../vendor/autoload.php';

try {
    $app = new App(
        // publicPath: web accessible directory. Must contain index.php.
        __DIR__,
        // projectPath: parent of publicPath if null.
        null,
        // projectNamespace: "Project" if null.
        null,
    );
    $app->run();
} catch (ApplicationException $e) {
    // Only the constructor can throw here, run() handles its own errors.
    echo $e->getMessage();
    exit;
}
```

With Apache, send every request that doesn't match a real file to `index.php`:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [L]
```

### CLI: `bin/cli`

```php
#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

try {
    $app = new \Project\App(__DIR__ . '/../public', null, null);
    $app->run();
} catch (\WebServCo\Framework\Exceptions\ApplicationException $e) {
    echo $e->getMessage();
    exit;
}
```

```shell
# Calls \Project\Domain\Import\Command::run('2024', 'full')
bin/cli Import/run 2024 full
```

The first argument is the target. Everything after it goes to the method as arguments (more in [Dispatching](#dispatching)).

---

## Your Application

`Application` works out of the box. It dispatches the request and has a basic error page, so most projects extend it and only add error reporting:

```php
namespace Project;

use Throwable;
use WebServCo\Framework\Application;

final class App extends Application
{
    // Log the error, then show the default error output.
    protected function haltHttp(Throwable $throwable): bool
    {
        (new ErrorProcessor())->report($throwable);

        return parent::haltHttp($throwable);
    }

    protected function haltCli(Throwable $throwable): bool
    {
        (new ErrorProcessor())->report($throwable);

        return parent::haltCli($throwable);
    }
}
```

`ErrorProcessor` is your implementation of [`AbstractErrorProcessor`](Log.md#error-processor).

Your own error pages go in `getHttpOutput(Throwable $throwable): string` (HTML) and `getCliOutput(Throwable $throwable): string`. Want different dispatching, say with middleware in front of the controllers? Extend `AbstractApplication` instead and write `getResponse(): ResponseInterface` yourself, calling the default dispatcher `execute()` when you're ready for the controller.

### OutputLoader

Controllers render through an output loader, which you create by extending `AbstractOutputLoader` and handing it the two output libraries:

```php
namespace Project;

use WebServCo\Framework\AbstractOutputLoader;
use WebServCo\Framework\Helpers\HtmlOutputLibraryHelper;
use WebServCo\Framework\Helpers\JsonOutputLibraryHelper;

final class OutputLoader extends AbstractOutputLoader
{
    public function __construct(string $projectPath)
    {
        parent::__construct(
            $projectPath,
            HtmlOutputLibraryHelper::library(),
            JsonOutputLibraryHelper::library(),
        );
    }
}
```

Templates then come from `<projectPath>resources/views/` ([HtmlOutput](Output/HtmlOutput.md) explains how).

---

## Request lifecycle

Here is what `run()` does, in order:

1. `ErrorHandler::set()`: every PHP warning, notice and deprecation is now thrown as an `\ErrorException`.
2. Registers `shutdown()` as the PHP shutdown function, so fatal errors are reported too.
3. Loads `config/.env.ini` into the environment settings. See [Configuration](Configuration.md).
4. `getResponse()`: in `Application` this is `execute()`, which finds and calls the controller method.
5. Checks for PHP errors that happened before the script started (for example a POST over `post_max_size`), and throws them as a `NonApplicationException`.
6. Sends the response and exits with its status (the exit code matters in CLI).

If anything throws, the `Throwable` lands in `shutdown()`, which calls `haltHttp()` or `haltCli()` and exits with status `1`.

Before any of this, the constructor has already stored three settings: `APP_PATH_WEB`, `APP_PATH_PROJECT` and `APP_PATH_LOG` (which is `<projectPath>var/log/`).

---

## Dispatching

`execute()` gets a route (class, method, arguments) from the [Router](Libraries/Router.md) and turns the class part into a full class name:

| Interface | Class |
| --- | --- |
| HTTP | `\<Namespace>\Domain\<class>\Controller` |
| CLI | `\<Namespace>\Domain\<class>\Command` |

So `/Blog/post/12` ends up in `\Project\Domain\Blog\Controller::post('12')`.

- The class is created with **no constructor arguments**. That means your controller needs a parameterless constructor, which passes an output loader up to the parent ([Controllers](Controllers.md) shows one).
- Arguments always arrive as strings, first the URL parts after the method, then any CLI arguments.
- A controller class that doesn't exist sends the request to the router's `404_route`, and without one you get a `NotFoundException` (404). For a missing `Command` it's a `NonApplicationException`.
- A missing method throws a `NonApplicationException`, which means a 500 page. The same happens for a method that also exists in a parent class (`setData`, `output`, ...).
- Whatever the method returns has to be a `ResponseInterface` (`Http\Response`, `Json\Response`, `Cli\Response`, ...).

---

## Error output

When the exception code is a [known status code](Http/Response.md#status-codes), `haltHttp()` sends it as the HTTP status. Anything else becomes a 500. In practice you throw `new NotFoundException('...')` from a controller and the visitor gets a 404 page, or `ForbiddenException` for a 403 ([Exceptions](Exceptions/Exceptions.md) lists them all).

The default page from `Application::getHttpOutput()` says "Resource not found" for a 404 and "Boo boo" for everything else. In `development` (the `APP_ENVIRONMENT` value) it also prints the message, file, line and stack trace.

In CLI, `Application::getCliOutput()` prints the message with the file name and line.

---

## Notes

The `@` operator won't help you here. `ErrorHandler` throws on suppressed warnings too, so check first (`is_readable()`, `array_key_exists()`) and skip the call.

Set `APP_ENVIRONMENT` in `config/.env.ini` even if you think you don't need it. `Application` reads it while rendering the error page, and a missing key at that point leaves you with an error inside the error handling.

Libraries are shared instances ([Controllers](Controllers.md#libraries) has the details), so tests should reset any library data they change.
