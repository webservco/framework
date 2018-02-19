# Cookie

## \WebServCo\Framework\Libraries\Cookie

## Instantiation

### Framework

Both `\WebServCo\Framework\AbstractController` and `\WebServCo\Framework\AbstractRepository` contain the method `cookie()` which returns a single instance of the Cookie class.

This means you can use `$this->cookie()` in your Controller or Repository classes.

The Library is instantiated using the configuration file `Cookie.php` from your application's `config` directory.

To use the library in another place:

```php
final public function cookie()
{
    return \\WebServCo\Framework\Framework::getLibrary('Cookie');
}
```

### Standalone instantiation

```php
$cookie = new \WebServCo\Framework\Libraries\Cookie($configurationArray);
```
## Usage

### Setting a value

> Note: the value is stored sanitized.

```php
$this->cookie()->set($name, $value);
$this->cookie()->set('username', 'Aria');
```
### Removing a value

```php
$this->cookie()->remove($name);
$this->cookie()->remove('username');
```

### Retrieving a value

> Note: the value is stored sanitized.

```php
$this->cookie()->get($name);
$this->cookie()->get('username');
```
