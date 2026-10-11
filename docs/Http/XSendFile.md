# X-Sendfile

`\WebServCo\Framework\Http\XSendFileResponse`

PHP never reads the file. It only tells the web server which file to send, which makes this the way to serve big downloads and private files kept outside the public directory.

- Requires: a web server module that handles the `X-Sendfile` header, such as Apache [mod_xsendfile](https://tn123.org/mod_xsendfile/)

> mod_xsendfile is a small Apache2 module that processes X-SENDFILE headers registered by the original output handler.
>
> If it encounters the presence of such header it will discard all output and send the file specified by that header instead using Apache internals including all optimizations like caching-headers and sendfile or mmap if configured.

---

## Usage

In a controller:

```php
use WebServCo\Framework\Http\XSendFileResponse;

return new XSendFileResponse(
    // filePath: full path on the server
    $filePath,
    // outputFilename: the name the browser saves it as
    'invoice-2024-001.pdf',
    // contentType, default "application/octet-stream"
    'application/pdf',
);
```

If PHP can't read the file, you get a `NotFoundException` (404).

---

## Configuration

Apache:

```apache
XSendFile On
XSendFilePath /var/www/project/var/files
```

---

## Notes

Forget the module and visitors download an empty file. The response body really is empty, and only the header knows where the file is.

It's always sent as an attachment (`Content-Disposition: attachment`). To show a file in the browser, or for small files that fit in memory anyway, use [Files](../Files/Files.md).

On Nginx the equivalent header is `X-Accel-Redirect`, which this class doesn't support.
