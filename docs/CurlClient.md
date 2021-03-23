# CurlClient

## `\WebServCo\Framework\Http\CurlClient`

Requires `\WebServCo\Framework\Interfaces\LoggerInterface`.

Returns `\WebServCo\Framework\Http\Response`.

## Initialization
```php
$curlClient = new \WebServCo\Framework\Http\CurlClient($loggerInterface);
```

---

## Usage

### `GET`
```php
$response = $curlClient->get($url);
```

### `POST`
```php
$response = $curlClient->post($url, $postData);
```

Default `Content-Type` for `POST` data is
* `application/x-www-form-urlencoded` when `$postData` is a string;
* `multipart/form-data` when `$postData` is an array;
For using a specific `Content-Type`, please use a custom request.

### Custom request

If request data is a string, the `Content-Length` Header is set automatically.

#### JSON Example

```php
$curlClient->setDebug(true);
$curlClient->setMethod(\WebServCo\Framework\Http\Method::POST);
$curlClient->setRequestContentType('application/json');
$curlClient->setRequestHeader('Accept', 'application/json');
$curlClient->setRequestData('{"foo": "bar"}');
$response = $curlClient->retrieve($url); // \WebServCo\Framework\Http\Response
return json_decode($response->getContent(), true);
```

---
