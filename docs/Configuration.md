# Configuration

Configuration lives in two places that have nothing to do with each other. Values that change from one server to the next go in an INI file outside git, and everything that shapes how the application behaves goes in PHP files you commit:

| | Environment settings | Library settings |
| --- | --- | --- |
| File | `config/.env.ini` | `config/<Library>.php` |
| Holds | values that change per server: environment, credentials | application behavior: routes, templates, languages |
| Commit to git | no | yes |
| Read with | `Environment\Config::string('APP_...')` | `$this->router()->setting('routes')` |

---

## Environment settings

`\WebServCo\Framework\Environment\Settings`, `Setting`, `Config`

`Application::run()` loads `config/.env.ini` before anything else and stores every key in `$_SERVER`, upper-cased.

```ini
[General]

app_environment = development

[Database]

app_dbms_host = db
app_dbms_port = 3306
app_dbms_dbname = db
app_dbms_username = db
app_dbms_passwd = "db"

[Custom]

app_mail_to_address = 'office@example.com'
app_feature_enabled = true
```

- Every key needs the `app_` prefix (any case). Anything else throws a `ConfigurationException`. Section names don't matter.
- Values get a type. `3306` becomes an `int`, `1.5` a `float`, and `true`, `on` or `yes` a `bool`. Quotes keep a value a string (`"123"`), which is easy to forget for a password made only of digits.
- A missing or unreadable file throws a `ConfigurationException`.

Many projects keep one file per environment (`.env.development.ini`, `.env.production.ini`) and copy or symlink the right one to `.env.ini` on deploy.

### Reading values

```php
use WebServCo\Framework\Environment\Config;

$host = Config::string('APP_DBMS_HOST');
$port = Config::int('APP_DBMS_PORT');
$enabled = Config::bool('APP_FEATURE_ENABLED');
// No type check.
$value = Config::key('APP_SOMETHING');
```

There's no default value. A missing key throws a `ConfigurationException`, and a value of the wrong type throws a `ConfigurationValidationException`.

### Setting values at runtime

```php
use WebServCo\Framework\Environment\Setting;

Setting::set('APP_ENVIRONMENT', 'testing');
```

### Keys used by the framework

| Key | Type | Set by | Used by |
| --- | --- | --- | --- |
| `APP_ENVIRONMENT` | string | you (required) | `Application` error page. One of `development`, `testing`, `staging`, `production` (`Values\Environment` constants), anything else throws an `EnvironmentException`. |
| `APP_PATH_WEB` | string | Application constructor | the public directory, with a trailing slash |
| `APP_PATH_PROJECT` | string | Application constructor | library settings files, `OutputLoader`, `I18n` |
| `APP_PATH_LOG` | string | Application constructor | `<projectPath>var/log/`, used by `AbstractErrorProcessor` |
| `APP_DBMS_HOST` | string | you | [PDO database](Database/PdoDatabase.md) |
| `APP_DBMS_PORT` | int | you | PDO database |
| `APP_DBMS_DBNAME` | string | you | PDO database |
| `APP_DBMS_USERNAME` | string | you | PDO database |
| `APP_DBMS_PASSWD` | string | you | PDO database |
| `APP_STRICT_CUSTOM_PATH` | bool | you (optional, default `true`) | [Session](Libraries/Session.md): throw if the custom storage path can't be set |

---

## Library settings

The first time a library is used, it reads `config/<LibraryName>.php`, a file that returns an array. Leave the file out and the library runs on its defaults.

```php
<?php

declare(strict_types=1);

return [
    'default_route' => ['App', 'home', []],
];
```

| File | Keys | Docs |
| --- | --- | --- |
| `HtmlOutput.php` | `main_template`, `partials` | [HtmlOutput](Output/HtmlOutput.md#configuration) |
| `I18n.php` | `domain`, `lang`, `langs` | [I18n](Libraries/I18n.md#configuration) |
| `Request.php` | `suffixes`, `clear_globals` | [Request](Libraries/Request.md#configuration) |
| `Router.php` | `default_route`, `404_route`, `routes` | [Router](Libraries/Router.md#configuration) |
| `Session.php` | `expire`, `cookie` | [Session](Libraries/Session.md#configuration) |
| `Cookie.php`, `JsonOutput.php`, `MysqlPdoDatabase.php`, `Security.php` | none read by the framework | your own keys, read with `setting()` |

You read a setting through the library that owns it:

```php
$routes = $this->router()->setting('routes', []);
// Nested keys, see ArrayStorage.
$secure = $this->session()->setting('cookie/secure', true);
```

Despite the name, the [Config library](Libraries/Config.md) doesn't give you these files. It's a separate key/value store.
