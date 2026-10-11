# File upload

Namespace `\WebServCo\Framework\Files\Upload`

| Class | Use |
| --- | --- |
| `AbstractUpload` | one file from `$_FILES` |
| `AbstractMultipleUpload` | several files from one field (`name="photos[]"`) |
| `AbstractFileUploadProcessor` | one file, with validation and processing hooks before and after saving |

Whichever you pick, failures come as an `\WebServCo\Framework\Exceptions\UploadException` with a code from `Codes` and a translated message.

- Requires: the [I18n library](../Libraries/I18n.md) loaded (`$this->i18n()`), because the error messages use `__()`

---

## Simple upload

The only method you have to write decides what the saved file is called:

```php
use WebServCo\Framework\Files\Upload\AbstractUpload;

final class Upload extends AbstractUpload
{
    protected function generateUploadedFileName(string $uploadFileName, string $uploadFileMimeType): string
    {
        // Never trust the uploaded name. allowedExtensions is set with setAllowedExtensions().
        return sprintf('%s.%s', bin2hex(random_bytes(16)), $this->allowedExtensions[$uploadFileMimeType] ?? 'bin');
    }
}
```

Then, in the controller:

```php
use WebServCo\Framework\Exceptions\UploadException;
use WebServCo\Framework\Files\Upload\Codes;
use WebServCo\Framework\Helpers\ErrorObjectHelper;

// An upload larger than post_max_size fails before the script starts, with empty $_POST and $_FILES.
// Check for it as early as possible.
$throwable = ErrorObjectHelper::get(null);
if ($throwable instanceof UploadException) {
    $this->setOutputCode(400);
    $this->setData('alert/message', $throwable->getMessage());

    return $this->outputHtml($this->getData(), 'alert/danger');
}

$upload = new Upload(
    // uploadDirectory, with a trailing slash
    sprintf('%svar/uploads/', $projectPath),
);
// Form field name. Default "upload".
$upload->setFormFieldName('photo');
// Allowed MIME types (mime => extension). Default: all.
$upload->setAllowedExtensions([
    'image/jpeg' => 'jpg',
    'image/pjpeg' => 'jpg',
    'image/png' => 'png',
    'image/x-png' => 'png',
]);

try {
    // false if nothing was uploaded at all ($_FILES is empty).
    if ($upload->do()) {
        // File name only, not the full path.
        $fileName = $upload->getFileName();
        $mimeType = $upload->getFileMimeType();
    }
} catch (UploadException $e) {
    // If the field is optional, "no file" is not an error.
    if ($e->getCode() !== Codes::NO_FILE) {
        $this->setData('alert/message', $e->getMessage());
    }
}
```

Don't forget `enctype="multipart/form-data"` on the form.

---

## Multiple upload

For several files, the input needs brackets and `multiple` (`<input type="file" name="photos[]" multiple>`), and your class extends `AbstractMultipleUpload`. The `generateUploadedFileName()` method stays exactly the same.

```php
$upload->setFormFieldName('photos');

try {
    $totalUploaded = $upload->getTotalUploaded();
    for ($index = 0; $index < $totalUploaded; $index += 1) {
        $upload->doItem($index);
        // File name only, not the full path.
        $fileName = $upload->getItemFileName($index);
        $mimeType = $upload->getItemFileMimeType($index);
    }
} catch (UploadException $e) {
    // ...
}
```

Leave out the `[]` and `getTotalUploaded()` throws an `\InvalidArgumentException`.

---

## Upload processor

`AbstractFileUploadProcessor` gets the allowed types through its constructor and `$_FILES` as an argument, which makes it much easier to test. It also has hooks that run before and after the file is saved:

```php
use WebServCo\Framework\Exceptions\UploadException;
use WebServCo\Framework\Files\Upload\AbstractFileUploadProcessor;
use WebServCo\Framework\Files\Upload\Codes;

final class PhotoUploadProcessor extends AbstractFileUploadProcessor
{
    protected function generateUploadedFileName(string $uploadFileName, string $uploadFileMimeType): string
    {
        return sprintf('%s.%s', bin2hex(random_bytes(16)), $this->allowedExtensions[$uploadFileMimeType]);
    }

    // Before saving: throw an UploadException to reject the file.
    protected function validateUploadedFileBeforeSaving(string $filePath): bool
    {
        $size = getimagesize($filePath);
        if ($size === false || $size[0] < 800) {
            throw new UploadException(Codes::IMAGE_TOO_SMALL);
        }

        return true;
    }

    // Before saving: change the temporary file (resize, strip metadata, ...).
    protected function processUploadedFileBeforeSaving(string $filePath): bool
    {
        return true;
    }

    // After saving: create thumbnails, store in the database, ...
    protected function processUploadedFileAfterSaving(string $filePath): bool
    {
        return true;
    }
}
```

```php
$processor = new PhotoUploadProcessor(['image/jpeg' => 'jpg', 'image/png' => 'png']);
// Saved file name, or null if $_FILES is empty.
$fileName = $processor->handleUpload('photo', $uploadDirectory, $_FILES);
```

---

## Error codes

`\WebServCo\Framework\Files\Upload\Codes`

| Constant | Code | Message |
| --- | --- | --- |
| `OK` | 0 | |
| `INI_SIZE`, `FORM_SIZE` | 1, 2 | Uploaded file size exceeds maximum file size allowed. |
| `PARTIAL` | 3 | The uploaded file was only partially uploaded. |
| `NO_FILE` | 4 | No file was uploaded. |
| `NO_TMP_DIR`, `EXTENSION` | 6, 8 | Upload error. |
| `CANT_WRITE` | 7 | Error saving uploaded file. |
| `TYPE_NOT_ALLOWED` | 91 | File type not allowed. |
| `IMAGE_TOO_SMALL` | 101 | Image is too small. |
| `IMAGE_WRONG_ASPECT_RATIO` | 102 | Image has wrong aspect ratio. |

`Codes::getMessage($code)` translates these through `__()`, so add them to your `.po` files.

---

## Notes

Don't trust the type check alone. It compares the MIME type the browser claims (`$_FILES[...]['type']`), and a client can claim anything. When it matters, inspect the file itself with `finfo` or `getimagesize()`, which is what `validateUploadedFileBeforeSaving()` is for.

Saved files end up as `0664` with `AbstractUpload` and `AbstractMultipleUpload`. `AbstractFileUploadProcessor` sets `umask(0002)` before saving instead.

A file that already exists under the same name gets overwritten without warning.

Create the upload directory beforehand and make it writable.
