# HTTP Response

`\WebServCo\Framework\Http\Response`

What a controller action returns. It carries the content, the status code and the headers.

- Implements: `Interfaces\ResponseInterface`
- Subclasses: `Json\Response` ([JSON](../Json.md)), `Http\XSendFileResponse` ([X-Sendfile](XSendFile.md))

---

## Usage

```php
use WebServCo\Framework\Http\Response;

return new Response(
    // content
    '<p>Hello</p>',
    // statusCode
    200,
    // headers: name => list of values
    [
        'Content-Type' => ['text/html'],
        'Cache-Control' => ['no-cache'],
    ],
);
```

You'll rarely write that by hand, since the [output methods](../Output/Methods.md) do it for you.

### Reading and changing

```php
$response->getContent();
$response->getStatus();
// ['text/html'] (names are case insensitive)
$response->getHeader('Content-Type');
// 'text/html' (values joined with ", ")
$response->getHeaderLine('Content-Type');

$response->setContent('...');
$response->setStatus(201);
$response->setHeader('x-custom', 'value');
```

### Sending

The application calls `send()` for you. It sends the status line, your headers and a `Content-length`, echoes the content, and returns the status code.

---

## Status codes

`\WebServCo\Framework\Http\StatusCode`

```php
use WebServCo\Framework\Http\StatusCode;

// [200 => 'OK', 404 => 'Not Found', ...]
$codes = StatusCode::getSupported();
```

The list has the standard codes from 100 to 511, plus Cloudflare's 525 ("SSL Handshake Failed"). Anything outside it makes `setStatus()`, and the constructor, throw an `HttpStatusCodeException`.

---

## Methods

`\WebServCo\Framework\Http\Method`

There's a constant for each of `GET`, `HEAD`, `POST`, `PUT`, `DELETE`, `CONNECT`, `OPTIONS` and `TRACE`, and `Method::getSupported()` returns all eight.

```php
use WebServCo\Framework\Http\Method;

if ($this->request()->getMethod() !== Method::POST) {
    throw new MethodNotAllowedException('POST only.');
}
```

---

## Notes

The constructor lowercases header names (`content-type`), which HTTP is fine with. `setHeader()` doesn't. Call `setHeader('Content-Type', ...)` on a response that got its `Content-Type` from the constructor and the header goes out twice, so stick to lower case with `setHeader()`.

You can store several values for one header and each goes out as its own header line. The first value replaces anything PHP had already queued under that name, the rest are appended. Cookies should go through the [Cookie library](../Libraries/Cookie.md) rather than `Set-Cookie` headers.
