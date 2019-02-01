# CurlBrowser

## \WebServCo\Framework\CurlBrowser

Requires `\WebServCo\Framework\Interfaces\LoggerInterface`.

Returns `\WebServCo\Framework\Http\Response`.

## Initialization
```php
$curlBrowser = new \WebServCo\Framework\CurlBrowser($loggerInterface);
```

---

## Usage

### `GET`
```php
$response = $curlBrowser->get($url);
```

### `POST`
```php
$response = $curlBrowser->post($url, $postData);
```

Default `Content-Type` for `POST` data is
* `application/x-www-form-urlencoded` when `$postData` is a string;
* `multipart/form-data` when `$postData` is an array;
For using a specific `Content-Type`, please use a custom request.

### Custom request

If request data is a string, the `Content-Length` Header is set automatically.

```php
$curlBrowser->setMethod(\WebServCo\Framework\Http\Method::POST);
$curlBrowser->setRequestContentType('application/json');
$curlBrowser->setPostData('{"foo": "bar"}');
$response = $curlBrowser->retrieve($url);
```