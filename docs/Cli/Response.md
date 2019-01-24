# Response

## \WebServCo\Framework\Cli\Response

## Usage

Last line in your Controller method:

```php
return new \WebServCo\Framework\Libraries\CliResponse('<content>', <statusCode>);
```

Success:

```php
return new \WebServCo\Framework\Libraries\CliResponse('Hello World' . PHP_EOL, 0);
```

Error:

```php
return new \WebServCo\Framework\Libraries\CliResponse('Error!' . PHP_EOL, 1);
```

It's also possible to use boolean for the status code:

```php
return new \WebServCo\Framework\Libraries\CliResponse('Hello World' . PHP_EOL, true);
```

```php
return new \WebServCo\Framework\Libraries\CliResponse('Error!' . PHP_EOL, false);
```

To simply return the object after successful custom output:
```php
return new \WebServCo\Framework\Libraries\CliResponse();
```
