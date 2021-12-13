# File upload

## Example

```php
class Upload extends \WebServCo\Framework\Files\Upload\AbstractUpload
{
    /* abstract */
    protected function generateUploadedFileName($uploadFileName, $uploadFileMimeType)
    {
        // functionality
    }
}
```

```php
// Handle errors that happen before PHP script execution
// Example: "Error: POST Content-Length of X bytes exceeds the limit of Y bytes in Unknown:0."
$throwable = \WebServCo\Framework\Helpers\ErrorObjectHelper::get(null);
if ($throwable instanceof \WebServCo\Framework\Exceptions\UploadException) {
    $this->setData('alert/message', $throwable->getMessage());
    $this->setOutputCode(400); // \WebServCo\Framework\Traits\OutputTrait
    $this->setData(
    'alert/back/url',
        \sprintf('%sshipment/info#form', $this->data('url/app', '/')),
    );
    return $this->outputHtml($this->getData(), 'alert/danger');
}

$allowedExtensions = [
    'image/jpeg' => 'jpg',
    'image/pjpeg' => 'jpg',
    'image/x-citrix-jpeg' => 'jpg',
    'image/png' => 'png',
    'image/x-png' => 'png',
    'image/x-citrix-png' => 'png',
];
$uploadDirectory = ...

$upload = new Upload($uploadDirectory);
$upload->setFormFieldName('upload');
$upload->setAllowedExtensions($allowedExtensions);

try {
    $upload->do(); // throws \WebServCo\Framework\Exceptions\UploadException
    $uploadedFileName = $upload->getFileName();
    // functionality
} catch (\WebServCo\Framework\Exceptions\UploadException $e) {
    // error handling
}
```
