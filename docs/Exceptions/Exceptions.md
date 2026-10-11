# Exceptions

Namespace `\WebServCo\Framework\Exceptions`

Every framework exception extends `ApplicationException`. If its code is a valid HTTP status, the error page uses it ([Error output](../Application.md#error-output) has the details). For a 404 or 403 page, throwing the right exception from the controller is all it takes.

---

## Hierarchy

| Exception | Parent | Code | Constructor |
| --- | --- | --- | --- |
| `ApplicationException` | `\Exception` | 500 | `(string $message, int $code = 500, ?Throwable $previous = null)` |
| `HttpException` | `ApplicationException` | 400 | `(string $message, int $code = 400, ?Throwable $previous = null)` |
| `AclException` | `HttpException` | 401 | `(string $message, int $code = 401, ?Throwable $previous = null)` |
| `UnauthorizedException` | `AclException` | 401 | `(string $message, ?Throwable $previous = null)` |
| `ForbiddenException` | `AclException` | 403 | same |
| `NotFoundException` | `HttpException` | 404 | same |
| `MethodNotAllowedException` | `HttpException` | 405 | same |
| `UnsupportedMediaTypeException` | `HttpException` | 415 | same |
| `SslRequiredException` | `HttpException` | 400 | same |
| `HttpClientException` | `HttpException` | 500 | same |
| `HttpStatusCodeException` | `HttpException` | 500 | same |
| `NotImplementedException` | `HttpException` | 501 | same |
| `Validation\ValidationException` | `ApplicationException` | 400 | same |
| `Validation\InvalidArgumentException` | `ValidationException` | 400 | same |
| `Validation\RequiredArgumentException` | `ValidationException` | 400 | same |
| `ConfigurationException` | `ApplicationException` | 500 | `(string $message, int $code = 500, ?Throwable $previous = null)` |
| `ConfigurationValidationException` | `ConfigurationException` | 500 | same as parent |
| `DateTimeException` | `ApplicationException` | 500 | same as parent |
| `EnvironmentException` | `ApplicationException` | 500 | same as parent |
| `NonApplicationException` | `ApplicationException` | 500 | same as parent |
| `DatabaseException` | `ApplicationException` | MySQL error number, or 0 | `(string $message, ?Throwable $previous = null)` |
| `ArrayStorageException` | `ApplicationException` | 0 | `(string $message, ?Throwable $previous = null)` |
| `FileException` | `ApplicationException` | 0 | same |
| `LoggerException` | `ApplicationException` | 0 | same |
| `SessionException` | `ApplicationException` | 0 | same |
| `RunnerException` | `ApplicationException` | 0 | same |
| `LibraryException` | `RunnerException` | 0 | same |
| `InterruptedException` | `RunnerException` | 0 | same |
| `AlreadyHappenedException` | `RunnerException` | 0 | same |
| `AlreadyRunningException` | `AlreadyHappenedException` | 0 | same |
| `AlreadyFinishedException` | `AlreadyHappenedException` | 0 | same |
| `UploadException` | `ApplicationException` | upload error code | `(int $code = -1, ?Throwable $previous = null)` |

Code 0 means a 500 page, and so does every `UploadException`.

---

## Usage

```php
use WebServCo\Framework\Exceptions\ForbiddenException;
use WebServCo\Framework\Exceptions\NotFoundException;

if (!$this->session()->has('user/id')) {
    throw new ForbiddenException('Login required.');
}

if (!$item) {
    throw new NotFoundException('Item not found.');
}
```

When you rethrow, pass the original as `$previous`. The [error processor](../Log.md#error-processor) walks the whole chain and logs each one.

---

## Notes

`NonApplicationException` covers problems the application has no way to deal with. A missing command or action is one. A PHP error from before the script even started (an oversized POST, say) is another.

`UploadException` is the odd one out. Its constructor takes an upload error code, and the message comes from `Files\Upload\Codes::getMessage()`, which calls the `__()` translation function. Load the [I18n library](../Libraries/I18n.md) before creating one, or `__()` won't exist yet ([File upload](../Files/Upload.md) has the rest).

`ApplicationException::__toString()` prints `WebServCo\Framework\Exceptions\ApplicationException: [code]: message`, with that class name even for subclasses.

Database errors have their own page, [DatabaseException](DatabaseException.md).
