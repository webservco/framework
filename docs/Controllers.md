# Controllers

`\WebServCo\Framework\AbstractController`, `\WebServCo\Framework\AbstractRepository`

Controllers answer HTTP requests and commands answer CLI ones. Both extend `AbstractController` and return a response, while the code that fetches data for them goes in a repository.

All three are [libraries](Libraries/AbstractLibrary.md) underneath, with `setData()`, `data()` and `getData()`. In a controller that data ends up in the template, since most actions finish with `$this->outputHtml($this->getData(), ...)`.

---

## Base controller

The framework creates controllers without arguments ([Dispatching](Application.md#dispatching) explains why). Shared setup therefore belongs in a base class of your own:

```php
namespace Project;

use WebServCo\Framework\AbstractController as FrameworkAbstractController;
use WebServCo\Framework\Environment\Config;
use WebServCo\Framework\Helpers\PhpHelper;

abstract class AbstractController extends FrameworkAbstractController
{
    public function __construct()
    {
        $projectPath = Config::string('APP_PATH_PROJECT');

        parent::__construct(new OutputLoader($projectPath));

        // Session::start() throws in CLI.
        if (!PhpHelper::isCli()) {
            $this->session()->start(sprintf('%svar/sessions', $projectPath));
        }

        $this->i18n()->init($projectPath);
    }
}
```

The `OutputLoader` class is in [Application](Application.md#outputloader).

---

## Controller

```php
namespace Project\Domain\Blog;

use Project\AbstractController;
use WebServCo\Framework\Exceptions\NotFoundException;
use WebServCo\Framework\Interfaces\ResponseInterface;

final class Controller extends AbstractController
{
    private Repository $repository;

    public function __construct()
    {
        parent::__construct();

        // $this->outputLoader comes from OutputTrait.
        $this->repository = new Repository($this->outputLoader);
    }

    // URL: /Blog/post/12
    public function post(string $id): ResponseInterface
    {
        $post = $this->repository->getPost((int) $id);
        if (!$post) {
            throw new NotFoundException('Post not found.');
        }

        $this->setData('post', $post);

        // resources/views/blog/post.php inside resources/views/layout.php
        return $this->outputHtml($this->getData(), 'blog/post');
    }
}
```

Any public method can be reached from a URL, unless a parent class defines it too. Make helpers `protected` or `private`.

---

## Command

```php
namespace Project\Domain\Import;

use Project\AbstractController;
use WebServCo\Framework\Cli\Response;
use WebServCo\Framework\Interfaces\ResponseInterface;

final class Command extends AbstractController
{
    // bin/cli Import/run 2024
    public function run(string $year): ResponseInterface
    {
        $this->outputCli(sprintf('Importing %s', $year));

        // Content, exit status.
        return new Response('', 0);
    }
}
```

For longer commands there's colored output ([Ansi](Cli/Ansi.md)), a [progress bar](Cli/Progress/Bar.md) and a [Runner](Cli/Runner/Runner.md) that lets you stop a running job. The response itself is covered in [CLI Response](Cli/Response.md).

---

## Repository

```php
namespace Project\Domain\Blog;

use WebServCo\Framework\AbstractRepository;

final class Repository extends AbstractRepository
{
    /**
     * @return array<string,float|int|string|null>
     */
    public function getPost(int $id): array
    {
        return $this->mysqlPdoDb()->getRow('SELECT id, title FROM post WHERE id = ?', [$id]);
    }
}
```

Repositories get the same libraries and output methods as controllers.

---

## Libraries

Inside controllers, repositories, forms and the application, `\WebServCo\Framework\Traits\ExposeLibrariesTrait` adds one method per library:

| Method | Returns | Docs |
| --- | --- | --- |
| `config()` | `Interfaces\ConfigInterface` | [Config](Libraries/Config.md) |
| `cookie()` | `Libraries\Cookie` | [Cookie](Libraries/Cookie.md) |
| `i18n()` | `Interfaces\I18nInterface` | [I18n](Libraries/I18n.md) |
| `mysqlPdoDb()` | `Interfaces\DatabaseInterface` | [PDO](Database/PdoDatabase.md) |
| `request()` | `Interfaces\RequestInterface` | [Request](Libraries/Request.md) |
| `router()` | `Libraries\Router` | [Router](Libraries/Router.md) |
| `security()` | `Libraries\Security` | [Security](Libraries/Security.md) |
| `session()` | `Interfaces\SessionInterface` | [Session](Libraries/Session.md) |

Namespaces are relative to `WebServCo\Framework\`.

The first call creates the library with the settings from `config/<Library>.php`. After that you always get the same instance back. Outside those classes, use the trait or call the helper directly:

```php
use WebServCo\Framework\Helpers\SessionLibraryHelper;

// \WebServCo\Framework\Libraries\Session
$session = SessionLibraryHelper::library();
```

Helpers (namespace `WebServCo\Framework\Helpers`): `ConfigLibraryHelper`, `CookieLibraryHelper`, `HtmlOutputLibraryHelper`, `I18nLibraryHelper`, `JsonOutputLibraryHelper`, `MysqlPdoDatabaseLibraryHelper`, `RequestLibraryHelper`, `RouterLibraryHelper`, `SecurityLibraryHelper`, `SessionLibraryHelper`.

---

## Output

These come from `\WebServCo\Framework\Traits\OutputTrait` ([Output methods](Output/Methods.md) has the full list).

```php
return $this->outputHtml($this->getData(), 'blog/post');
return $this->outputJson(['id' => 12]);
```

For a status other than 200, set it before the output call:

```php
$this->setOutputCode(400);
return $this->outputHtml($this->getData(), 'alert/danger');
```

---

## Redirects

The redirect helpers in `\WebServCo\Framework\Traits\ResponseUrlTrait` build an `Http\Response`, and your action returns it.

```php
// To an application location: <appUrl>blog/list (+ the request suffix, for example ".html").
return $this->getRedirectResponse('blog/list');
// Without the suffix.
return $this->getRedirectResponse('blog/list', false);

// Reload the current URL, optionally without some query parameters.
return $this->getReloadResponse(['page']);

// To a full URL. 303 See Other by default.
return $this->getRedirectUrlResponse('https://example.com/', 301);
```

After a POST, stick with the default 303. The browser follows it with a GET, and a refresh won't submit the form again.
