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
    // functionality
} catch (\WebServCo\Framework\Exceptions\UploadException $e) {
    // error handling
}
```
