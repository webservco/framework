<?php

declare(strict_types=1);

namespace WebServCo\Framework\Libraries;

final class Router extends \WebServCo\Framework\AbstractLibrary
{
    /**
    * @param string $requestCustom
    * @param array<string,string> $routes
    * @param array<int,string> $extraArgs
    * @return array<array<int,string>|string>
    */
    public function getRoute(string $requestCustom, array $routes, array $extraArgs = []): array
    {
        $routeString = $this->parseCustomRoutes($requestCustom, $routes);
        if (empty($routeString) || 'index' == $routeString) {
            $defaultRoute = $this->setting('default_route');
            if (!isset($defaultRoute[1])) {
                throw new \WebServCo\Framework\Exceptions\ApplicationException(
                    "Default route missing or not valid."
                );
            }
            return $defaultRoute;
        }

        $parts = explode('/', $routeString, 3);

        if (empty($parts[1])) {
            throw new \WebServCo\Framework\Exceptions\NotFoundException(
                sprintf('The requested resource "%s" was not found.', $routeString)
            );
        }

        $controller = $parts[0];
        $action = $parts[1];
        $args = [];

        if (!empty($parts['2'])) {
            $args = explode('/', $parts[2]);
        }
        if (!empty($extraArgs)) {
            foreach ($extraArgs as $k => $v) {
                $args[] = $v;
            }
        }
        return [$controller, $action, $args];
    }

    /**
    * @param string $requestCustom
    * @param array<string,string> $routes
    * @return string
    */
    private function parseCustomRoutes(string $requestCustom, array $routes): string
    {
        if (is_array($routes)) {
            foreach ($routes as $k => $v) {
                 $k = str_replace(['{num}','{any}'], ['[0-9]+','.+'], $k);
                 /**
                  * Check for a custom route match.
                  */
                if (preg_match("#^{$k}$#", $requestCustom) ||
                preg_match("#^{$k}/$#", $requestCustom)
                ) {
                    /**
                     * Check for back references.
                     */
                    if (false !== strpos($v, '$') && false !== strpos($k, '(')) {
                        /**
                         * Parse request.
                         */
                         $v = preg_replace("#^{$k}$#", $v, $requestCustom);
                    }
                    return (string) $v;
                }
            }
        }
        return $requestCustom;
    }
}
