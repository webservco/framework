# File upload

Advanced functionality (pre-processing): `AbstractFileUploadProcessor`.

Simple functionality:

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
// Should be placed as early in the code as possible
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
$upload->setAllowedExtensions($allowedExtensions); // or set in the AbstractUpload implementing class constructor

try {
    $upload->do(); // throws \WebServCo\Framework\Exceptions\UploadException
    $uploadedFileName = $upload->getFileName(); // file name only, not complete path
    // functionality
} catch (\WebServCo\Framework\Exceptions\UploadException $e) {
    // error handling
    // If upload field is not the only form field, and optional, further validation is needed:
    //$uploadErrorCode = $e->getCode();
    //if (4 !== $uploadErrorCode) { // "No file was uploaded". No problem if field not mandatory.
}
```
