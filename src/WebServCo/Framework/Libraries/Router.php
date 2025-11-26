<?php

declare(strict_types=1);

namespace WebServCo\Framework\Libraries;

use WebServCo\Framework\AbstractLibrary;
use WebServCo\Framework\Exceptions\NotFoundException;
use WebServCo\Framework\Helpers\ArrayHelper;
use WebServCo\Framework\Objects\Route;

use function array_key_exists;
use function explode;
use function is_array;
use function preg_match;
use function preg_replace;
use function str_replace;
use function strpos;

final class Router extends AbstractLibrary
{
    public function getFourOhfourRoute(): Route
    {
        // Check if we have a 404 route
        $fourOhfourRoute = $this->setting('404_route', []);
        if (!isset($fourOhfourRoute[1])) {
            // No 404 route found, throw 404 exception.
            throw new NotFoundException('The requested resource was not found.');
        }

        return new Route(
            $fourOhfourRoute[0],
            $fourOhfourRoute[1],
            ArrayHelper::get($fourOhfourRoute, 2, []),
        );
    }

    /**
    * @param array<string,string> $routes
    * @param array<int,string> $extraArgs
    */
    public function getRoute(string $requestCustom, array $routes, array $extraArgs = []): Route
    {
        $routeString = $this->parseCustomRoutes($requestCustom, $routes);
        if (!$routeString || $routeString === 'index') {
            $defaultRoute = $this->setting('default_route', []);
            if (!isset($defaultRoute[1])) {
                throw new NotFoundException("Default route missing or not valid.");
            }

            return new Route(
                $defaultRoute[0],
                $defaultRoute[1],
                ArrayHelper::get($defaultRoute, 2, []),
            );
        }

        $parts = explode('/', $routeString, 3);

        $class = ArrayHelper::get($parts, 0, false);
        $method = ArrayHelper::get($parts, 1, false);
        if (!$class || !$method) {
            // Route is invalid
            // Return 404 route
            // throws \WebServCo\Framework\Exceptions\NotFoundException
            return $this->getFourOhfourRoute();
        }

        $args = array_key_exists(2, $parts)
            ? explode('/', $parts[2])
            : [];

        foreach ($extraArgs as $k => $v) {
            $args[] = $v;
        }

        return new Route($class, $method, $args);
    }

    /**
    * @param array<string,string> $routes
    */
    private function parseCustomRoutes(string $requestCustom, array $routes): string
    {
        if (is_array($routes)) {
            foreach ($routes as $k => $v) {
                 $k = str_replace(['{num}', '{any}'], ['[0-9]+', '.+'], $k);
                 /**
                  * Check for a custom route match.
                  */
                if (!preg_match("#^{$k}$#", $requestCustom) && !preg_match("#^{$k}/$#", $requestCustom)) {
                    continue;
                }

                /**
                 * Check for back references.
                 */
                if (strpos($v, '$') !== false && strpos($k, '(') !== false) {
                    /**
                     * Parse request.
                     */
                     $v = preg_replace("#^{$k}$#", $v, $requestCustom);
                }

                return (string) $v;
            }
        }

        return $requestCustom;
    }
}
