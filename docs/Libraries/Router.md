# Router

## `WebServCo\Framework\Libraries\Router`

Configuration example

```php
return [
    /**
     * Default route to use when nothing is matched (index or home page).
     * ['ControllerName', 'methodName', ['arg1', 'arg2']],
     */
    'default_route' => ['App', 'home', []],
    /**
    * Route to use for 404 (not found) response
    */
    '404_route' => ['App', 'fourOhfour', []],
    /**
     * Custom route aliases.
     * Do not add the dummy extension here.
     */
    'routes' => [
        'about' => 'App/about',
        'lists/({num})' => 'Lists/item/$1',
        'u/({any})' => 'User/profile/$1',
    ],
];

```

Note: If you'd rather use a custom/external page for 404, simply omit the `404_route` and the system will throw a `\WebServCo\Framework\Exceptions\NotFoundException` which you can catch in your Application.
