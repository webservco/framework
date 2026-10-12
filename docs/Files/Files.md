# Files

Namespace `\WebServCo\Framework\Files`

Wrap some data you already have in memory, a generated PDF for instance, and get back a response that either downloads it as an attachment or shows it in the browser.

- Implements: `Interfaces\FileInterface`

---

## Classes

| Class | Content type |
| --- | --- |
| `File` | `application/octet-stream`, the one given, or detected (`null`) |
| `CsvFile` | `text/csv`, the one given, or detected (`null`), see [CSV](CSV.md) |
| `PdfFile` | `application/pdf`, the one given, or detected (`null`) |
| `Mp3File` | `audio/mpeg`, the one given, or detected (`null`) |
| `XmlFile` | `text/xml` |
| `XmlFileFromDomElement` | `text/xml` |

---

## Usage

```php
use WebServCo\Framework\Files\File;
use WebServCo\Framework\Files\PdfFile;

$file = new File(
    // fileName: the name the browser uses
    'report.txt',
    // fileData: the content
    $content,
    // contentType: null to detect it from the data (ext-fileinfo)
    'text/plain',
);

// Download.
return $file->getDownloadResponse();
```

```php
// Show in the browser.
$pdfFile = new PdfFile('invoice.pdf', $pdfData);
return $pdfFile->getOutputResponse();
```

```php
// Detect the content type.
$file = new File('photo', $imageData, null);
// image/jpeg
$contentType = $file->getContentType();
```

### XML

```php
use WebServCo\Framework\Files\XmlFile;
use WebServCo\Framework\Files\XmlFileFromDomElement;

// Parses and saves the XML again. Pretty printed if the last argument is true.
$xmlFile = new XmlFile('feed.xml', $xmlString, true);

// From a DOMElement.
$xmlFile = new XmlFileFromDomElement('item.xml', $domElement, true);

return $xmlFile->getDownloadResponse();
```

### Other methods

```php
$file->getFileName();
$file->getFileData();
$file->getContentType();
$file->setFileName('other.txt');
```

---

## Responses

Both responses send `Cache-Control: public`, an `ETag` made from the MD5 of the data, and a `Last-Modified` set to the current time. Downloads also get `Content-Disposition: attachment; filename="<fileName>"`.

Everything here sits in memory. A 2 GB video belongs with [X-Sendfile](../Http/XSendFile.md).

---

## Notes

Each class defaults to its own `CONTENT_TYPE` constant. The two XML classes always use `text/xml` and don't take a content type.

`XmlFile` runs the data through `DOMDocument::loadXML()`. Broken XML triggers a warning there, and the framework's error handler turns that into an `\ErrorException`.

File names go into the header unchanged. Strip quotes and new lines from anything a user typed.
