# CurlClient

`\WebServCo\Framework\Http\CurlClient`

A cURL-based HTTP client that hands back an `Http\Response`.

- Implements: `Interfaces\HttpClientInterface`
- Requires: `ext-curl`, a [PSR-3](https://www.php-fig.org/psr/psr-3/) logger (used for debug output)

---

## Initialization

```php
use WebServCo\Framework\Http\CurlClient;
use WebServCo\Framework\Log\FileLogger;

$curlClient = new CurlClient(new FileLogger('http-client', $logDirectory));
```

Any `\Psr\Log\LoggerInterface` will do ([Log](../Log.md) has the framework's own).

---

## Usage

### `GET`

```php
// \WebServCo\Framework\Http\Response
$response = $curlClient->get($url);
$statusCode = $response->getStatus();
$body = $response->getContent();
$contentType = $response->getHeaderLine('content-type');
```

### `HEAD`

```php
$response = $curlClient->head($url);
```

### `POST`

```php
$response = $curlClient->post($url, $postData);
```

What you pass decides the `Content-Type`:

- An array goes out as `multipart/form-data`. Values have to be scalar, and a nested array throws an `\InvalidArgumentException`.
- A string goes out as `application/x-www-form-urlencoded` (cURL's default) unless you picked another type with `setRequestContentType()`. `Content-Length` is added for you.

### Custom request

```php
use WebServCo\Framework\Http\Method;

$curlClient->setMethod(Method::POST);
$curlClient->setRequestContentType('application/json');
$curlClient->setRequestHeader('Accept', 'application/json');
$curlClient->setRequestHeader('Authorization', sprintf('Bearer %s', $token));
$curlClient->setRequestData((string) json_encode(['foo' => 'bar']));
$response = $curlClient->retrieve($url);

$data = json_decode($response->getContent(), true);
```

For `PUT` or `DELETE`, swap in `setMethod(Method::PUT)` or `setMethod(Method::DELETE)`.

### Options

```php
// Seconds for the whole request. Default 60. (The connection timeout is always 60.)
$curlClient->setTimeout(10);
// Don't verify the SSL certificate. For development only.
$curlClient->setSkipSSlVerification(true);
// Log cURL info, verbose output, request data and response at debug level.
$curlClient->setDebug(true);
```

### Several requests with one client

A request doesn't clean up after itself. Headers, data and content type are still set for the next one unless you clear them:

```php
$response1 = $curlClient->post($url1, $data1);
$curlClient->reset();
$response2 = $curlClient->get($url2);
```

So does the method. `get()`, `head()` and `post()` set it, and `retrieve()` reuses whichever was set last.

### Response headers

`$response` only has the headers of the final response, after any redirects. For the whole chain:

```php
// One array per response (redirects included), header name (lower case) => list of values.
$headers = $curlClient->getResponseHeaders();
```

`reset()` leaves this list alone, so a client you reuse keeps collecting headers from earlier requests too. The last entry is always the latest response.

---

## Notes

Redirects are followed automatically.

Only cURL failures throw (DNS, connection, timeout), as an `HttpClientException`. A 404 or a 500 from the server comes back as a normal response, and checking `$response->getStatus()` is up to you.

There's one odd failure. A server answering with a status that isn't in the [supported list](Response.md#status-codes), a `418` for instance, makes the `Response` constructor throw an `HttpStatusCodeException`.

`setMethod()` accepts every `Http\Method`, yet only `POST`, `PUT` and `DELETE` send data and `HEAD` drops the body. `OPTIONS`, `CONNECT` and `TRACE` quietly go out as `GET`.

Whitespace around the response body is trimmed.
