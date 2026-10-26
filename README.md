# webservco/framework

A PHP MVC Framework for web applications and CLI commands. 

Requests are routed to controller methods and pages are plain PHP templates. 
Sessions, gettext translations, PDO database helpers, forms, file uploads and downloads, a cURL client and a file logger are all included, so a typical project needs very little else.

> **Note:** This framework is considered legacy. It has been replaced by individual components, please see [webservco/sandbox](https://webservco.github.io/sandbox/).

History before 2017: [webservco-legacy/web-2012-2017](https://github.com/webservco-legacy/web-2012-2017).


- Requires: PHP 8.3+, `ext-fileinfo`, `psr/log`
- Optional: `ext-pdo` (MySQL or PostgreSQL driver), `ext-curl`, `ext-gettext`, `ext-dom`, `ext-mbstring`

---

## Installation

```shell
composer require webservco/framework
```

---

## Quick start

Projects follow the [PDS skeleton](https://github.com/php-pds/skeleton) layout:

```
bin/cli                          CLI entry point
config/.env.ini                  environment settings (not committed)
config/Router.php                library settings, one file per library
public/index.php                 web entry point
resources/views/                 PHP templates
resources/translations/          gettext translations
src/Project/App.php              your Application
src/Project/OutputLoader.php     your OutputLoader
src/Project/Domain/App/Controller.php
src/Project/Domain/App/Command.php
var/log/                         log files
```

`public/index.php`:

```php
use Project\App;
use WebServCo\Framework\Exceptions\ApplicationException;

require __DIR__ . '/../vendor/autoload.php';

try {
    $app = new App(
        // publicPath: web accessible directory.
        __DIR__,
        // projectPath: parent of publicPath if null.
        null,
        // projectNamespace: "Project" if null.
        null,
    );
    $app->run();
} catch (ApplicationException $e) {
    echo $e->getMessage();
    exit;
}
```

`src/Project/App.php`:

```php
namespace Project;

use WebServCo\Framework\Application;

final class App extends Application
{
}
```

`config/.env.ini`:

```ini
app_environment = development
```

`config/Router.php`:

```php
return [
    'default_route' => ['App', 'home', []],
    'routes' => [],
];
```

`src/Project/Domain/App/Controller.php`:

```php
namespace Project\Domain\App;

use Project\OutputLoader;
use WebServCo\Framework\AbstractController;
use WebServCo\Framework\Environment\Config;
use WebServCo\Framework\Interfaces\ResponseInterface;

final class Controller extends AbstractController
{
    // The framework creates the controller with no arguments.
    public function __construct()
    {
        parent::__construct(new OutputLoader(Config::string('APP_PATH_PROJECT')));
    }

    public function home(): ResponseInterface
    {
        $this->setData('title', 'Hello World');

        // Renders resources/views/app/home.php inside resources/views/layout.php
        return $this->outputHtml($this->getData(), 'app/home');
    }
}
```

The last piece is an `OutputLoader`. It's about ten lines, and [Application](docs/Application.md#outputloader) has the full class.

---

## Documentation

### Basics

- [Application](docs/Application.md): project layout, entry points, request lifecycle, dispatching, error pages
- [Configuration](docs/Configuration.md): `config/.env.ini`, library settings files, every key the framework reads
- [Controllers](docs/Controllers.md): controllers, commands, repositories, redirects
- [ArrayStorage](docs/ArrayStorage.md): the `'a/b/c'` key syntax used by libraries, sessions and forms
- [Exceptions](docs/Exceptions/Exceptions.md): hierarchy, codes, HTTP status mapping
    - [DatabaseException](docs/Exceptions/DatabaseException.md)

### Libraries

- [AbstractLibrary](docs/Libraries/AbstractLibrary.md): data and settings storage shared by all libraries
- [Config](docs/Libraries/Config.md)
- [Cookie](docs/Libraries/Cookie.md)
- [Internationalization](docs/Libraries/I18n.md)
- [Request](docs/Libraries/Request.md)
- [Router](docs/Libraries/Router.md)
- [Security](docs/Libraries/Security.md)
- [Session](docs/Libraries/Session.md)

### Output

- [Output methods](docs/Output/Methods.md): `outputHtml()`, `outputJson()`, `outputCli()` and friends
- [HtmlOutput](docs/Output/HtmlOutput.md): templates, layout, partials
- [JSON](docs/Json.md)

### HTTP

- [Response](docs/Http/Response.md): `Http\Response`, status codes, methods
- [CurlClient](docs/Http/CurlClient.md)
- [X-Sendfile](docs/Http/XSendFile.md)

### CLI

- [Response](docs/Cli/Response.md)
- [Ansi](docs/Cli/Ansi.md): colors and styles
- [Progress\Bar](docs/Cli/Progress/Bar.md)
- [Progress\Line](docs/Cli/Progress/Line.md)
- [Runner](docs/Cli/Runner/Runner.md): stoppable long running commands

### Database

- [PDO](docs/Database/PdoDatabase.md): MySQL and PostgreSQL
- [AbstractObject](docs/Database/AbstractObject.md)
- [DataTables](docs/DataTables.md): server side processing for [DataTables](https://datatables.net/)

### Files

- [Files](docs/Files/Files.md): download and inline responses, PDF, MP3, XML
- [CSV](docs/Files/CSV.md)
- [File upload](docs/Files/Upload.md)

### Forms

- [AbstractForm](docs/Forms/AbstractForm.md)

### Logging

- [Log](docs/Log.md): file and output loggers, error processor, CLI processor

### Helpers

- [ArrayHelper](docs/Helpers/ArrayHelper.md)
- [DateHelper](docs/Helpers/DateHelper.md)
- [RequestHelper](docs/Helpers/RequestHelper.md)
- [StringHelper](docs/Helpers/StringHelper.md)
- [Other helpers](docs/Helpers/OtherHelpers.md): PHP, environment, templates, errors, HTTP headers

### Objects

- [Objects](docs/Objects.md): `fromArray()`, `toArray()`, validated classes

### Development

- [Development](docs/Development.md)

---

## Related projects

### [webservco/webapp-skeleton](https://github.com/webservco/webapp-skeleton)

A simple webservco/framework Web Application Skeleton to help start a new project fast.

### [webservco/project-skeleton](https://github.com/webservco/project-skeleton)

A simple webservco/framework Project Skeleton.
This is a blank project with no initial functionality.

### [webservco/api](https://github.com/webservco/api)

A PHP RESTful API Framework

---

## Development

```shell
# Start project.
ddev start
# Install dependencies.
ddev c u
# Run all validations.
ddev c c
# Run unit tests.
ddev c test:d
```

More in [Development](docs/Development.md).

---

## License

[MIT](LICENSE)
