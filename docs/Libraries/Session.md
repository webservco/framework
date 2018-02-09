# Session

## \WebServCo\Framework\Libraries\Session

## Initialization

### Framework

Both `\WebServCo\Framework\AbstractController` and `\WebServCo\Framework\AbstractRepository` contain the method `session()` which returns a single instance of the Session class.

So you can use `$this->session()` in your Controller or Repository classes.

The Library is initialized using the configuration file `Session.php` from your application's `config` directory.

To use the library in another place:

```php
final public function session()
{
    return \\WebServCo\Framework\Framework::getLibrary('Session');
}
```

### Standalone initialization

Standalone initialization is also possible:

```php
$session = new \WebServCo\Framework\Libraries\Session($configurationArray);
```

## Starting the session

In your Controller's `__construct()` method:

### with custom storage directory (recommended):

```php
$this->session()->start($storagePath);

$this->session()->start(
    $this->config()->get('app/path/project') . 'var/sessions'
);
```

### with default PHP storage directory:

```php
$this->session()->start();
```

## Usage

### Setting a value

```php
$this->session()->set($setting, $value);
$this->session()->set('user/name', 'Aria');
```
### Clearing a value

The key remains in session storage, however the value will be `null`.

```php
$this->session()->clear($setting);
$this->session()->clear('user/name');
```

### Removing a value

The key is deleted from session storage.

```php
$this->session()->remove($setting);
$this->session()->remove('user/name');
```

### Retrieving data

```php
$this->session()->get($setting, $defaultValue = false);
$this->session()->get('user/name');
$this->session()->get('user/name', 'N/A');
```