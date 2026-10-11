# Router

`\WebServCo\Framework\Libraries\Router`

The router turns a request target into a class name, a method name and a list of arguments. With those, the [Application](../Application.md#dispatching) calls `\Project\Domain\<class>\Controller::<method>(...$arguments)`, or the `Command` class in CLI.

---

## Instantiation

In controllers, repositories and forms: `$this->router()`. Elsewhere: `\WebServCo\Framework\Helpers\RouterLibraryHelper::library()`.

---

## Configuration

`config/Router.php`:

```php
return [
    /**
     * Default route, used when the target is empty or "index" (the home page).
     * ['ClassName', 'methodName', ['arg1', 'arg2']]
     * Required.
     */
    'default_route' => ['App', 'home', []],
    /**
     * Route used when the controller class doesn't exist, or the target has no method part.
     * Optional: without it a NotFoundException (404) is thrown.
     */
    '404_route' => ['App', 'notFound', []],
    /**
     * Custom routes: pattern => 'Class/method/arguments'.
     * Don't add the suffix (".html") here, it is removed before routing.
     */
    'routes' => [
        'about' => 'App/about',
        'blog' => 'Blog/list',
        'blog/({num})' => 'Blog/post/$1',
        'u/({any})' => 'User/profile/$1',
        'cart/({num})-({num})' => 'Cart/add/$1/$2',
    ],
];
```

---

## Matching

1. The [target](Request.md) is checked against each `routes` pattern, top to bottom, and the first match wins.
2. No match? Then the target is read directly as `Class/method/arg1/arg2/...`.
3. Either way, the first part names the class and the second the method. Anything after that becomes arguments.

Each pattern is a regular expression that has to match the whole target. Two placeholders save some typing:

| Placeholder | Regular expression | Matches |
| --- | --- | --- |
| `{num}` | `[0-9]+` | digits |
| `{any}` | `.+` | anything, including `/` |

Parentheses capture, and `$1`, `$2` and so on put the captured values into the target.

| Request | Route | Call |
| --- | --- | --- |
| `/` | default route | `App\Controller::home()` |
| `/about.html` | `about` | `App\Controller::about()` |
| `/blog/12` | `blog/({num})` | `Blog\Controller::post('12')` |
| `/cart/3-2` | `cart/({num})-({num})` | `Cart\Controller::add('3', '2')` |
| `/u/aria/photos` | `u/({any})` | `User\Controller::profile('aria', 'photos')` |
| `/Blog/post/12` | no route, used as is | `Blog\Controller::post('12')` |
| `/blog/post/12` | no route, used as is | class `Project\Domain\blog\Controller` |
| `/Blog` | no method part | 404 route |

CLI arguments after the target are appended to the list, so `bin/cli Blog/post 12` calls `Blog\Command::post('12')`.

---

## Usage

Normally the application does all of this for you. If you need a route yourself:

```php
// \WebServCo\Framework\Objects\Route
$route = $this->router()->getRoute(
    'blog/12',
    $this->router()->setting('routes', []),
    // Extra arguments, added at the end.
    [],
);
// "Blog", "post", ['12']
echo $route->class, $route->method;
$arguments = $route->arguments;

// The 404 route. Throws a NotFoundException if not configured.
$route = $this->router()->getFourOhfourRoute();
```

---

## Notes

The class part goes into the class name unchanged, case included. On Linux `/blog/post/12` won't find `src/Project/Domain/Blog/Controller.php`, so the visitor lands on the 404 route, and lowercase URLs need a route of their own.

Only two levels come before the arguments, so `Admin/User/edit` calls `Admin\Controller::User('edit')` and there's no way to nest domains.

The router never checks that the method exists. The application does that later, and a missing method there throws a `NonApplicationException`, which is a 500. If unknown methods should give a 404, list your routes explicitly.

Patterns sit inside `#...#` delimiters, so escape any `#` in them.

For a 404 page that lives outside the framework, leave out `404_route` and catch the `NotFoundException` in your Application's `haltHttp()`.
