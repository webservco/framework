# Request

`\WebServCo\Framework\Libraries\Request`

Everything about the current request, HTTP or CLI. That covers the method, the target (the path the router uses), the query, POST data, the raw body, URLs and server details. String values from `$_SERVER`, the query and POST field names are sanitized on the way in.

- Implements: `Interfaces\RequestInterface`

---

## Instantiation

In controllers, repositories and forms: `$this->request()`. Elsewhere: `\WebServCo\Framework\Helpers\RequestLibraryHelper::library()`.

It reads `$_SERVER` and `$_POST` once, the first time you use it. In tests you'll want your own values instead:

```php
use WebServCo\Framework\Libraries\Request;

$request = new Request(
    // settings
    ['suffixes' => ['.html']],
    // server
    ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/blog/post/12.html?page=2', 'SCRIPT_NAME' => '/index.php'],
    // post
    [],
);
```

---

## Configuration

`config/Request.php`:

```php
return [
    // Fake extensions removed from the URL before routing: /about.html is routed as "about".
    'suffixes' => ['.htm', '.html', '.php'],
    // Empty $_GET, $_POST (and the $_GET keys from $_REQUEST) after reading them. Default false.
    'clear_globals' => false,
];
```

---

## Usage

### Request data

```php
// GET, POST, PUT, ... ('' if missing or not a known method)
$method = $this->request()->getMethod();

// Routing target, without the base path, the suffix and the query: "blog/post/12"
$target = $this->request()->getTarget();
// The suffix that was removed: ".html"
$suffix = $this->request()->getSuffix();

// POST form fields (only for POST requests).
$email = $this->request()->data('email');
$email = $this->request()->data('email', '');
$post = $this->request()->getData();

// Query parameters.
$page = $this->request()->query('page', 1);
$query = $this->request()->getQuery();

// Raw body (php://input), for JSON requests for example.
$json = json_decode($this->request()->getBody(), true);

// CLI arguments after the target.
$args = $this->request()->getArgs();
```

### URLs

Take `https://example.com/app/blog/post/12.html?page=2&sort=date`, with the project installed in `/app`:

```php
// https://example.com/app/
$this->request()->getAppUrl();
// https://example.com/app/blog/post/12.html
$this->request()->getShortUrl();
// https://example.com/app/blog/post/12.html?page=2&sort=date
$this->request()->getUrl();
// https://example.com/app/blog/post/12.html?sort=date
$this->request()->getUrl(['page']);
```

In CLI there's no URL, and all four return `''`.

### Server information

```php
$this->request()->getHost();               // example.com (HTTP_HOST, SERVER_NAME, or HOSTNAME in CLI)
$this->request()->getHostExtension();      // com
$this->request()->getSchema();             // https (also behind a proxy: X-Forwarded-Proto, X-Forwarded-Ssl)
$this->request()->getServerProtocol();     // HTTP/1.1
$this->request()->getRemoteAddress();      // REMOTE_ADDR, or this machine's IP in CLI
$this->request()->getUserAgent();          // '' in CLI
$this->request()->getReferer();            // HTTP_REFERER
$this->request()->getRefererHost();        // host part of the referer
$this->request()->getContentType();        // CONTENT_TYPE
$this->request()->getAcceptLanguage();     // first two letters of Accept-Language: "en"
$this->request()->getAcceptContentTypes(); // ['q=1' => 'text/html', 'q=0.9' => '*/*', ...]

// Any $_SERVER value. Throws an \OutOfBoundsException if missing.
$this->request()->getServerVariable('HTTP_X_REQUESTED_WITH');
```

---

## Query string format

The framework parses the query string itself and ignores `$_GET`. `?page=2&sort=date` turns into `['page' => '2', 'sort' => 'date']`, and three details differ from what PHP would give you:

- A parameter with no value (`?debug`) is dropped.
- Keys and values are sanitized after URL decoding, so an encoded `%3C` gets the same treatment as a literal `<`.
- Brackets mean nothing. `?ids[]=1` produces a key literally named `ids[]`.

---

## Notes

`data()` holds POST form fields and nothing else, meaning `application/x-www-form-urlencoded` or `multipart/form-data`. A PUT, a DELETE or a POST with a JSON body has to be read from `getBody()`.

Sanitizing goes through [RequestHelper::sanitizeString()](../Helpers/RequestHelper.md#sanitizestring). It strips tags, backticks, backslashes, `$`, tabs and new lines and encodes quotes as `&#39;` and `&#34;`. POST **values** skip this step (only their keys are cleaned), and you should escape output in templates regardless.

`getAcceptContentTypes()` uses the quality as the array key. Two types with the same quality collide, and the second one wins.

Behind a proxy or load balancer, `getRemoteAddress()` gives you the proxy's address, because it reads `REMOTE_ADDR`.
