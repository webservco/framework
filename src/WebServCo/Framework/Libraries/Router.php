<?php
namespace WebServCo\Framework\Libraries;

final class Router extends \WebServCo\Framework\AbstractLibrary
{
    final public function getRoute($requestCustom, $routes)
    {
        $routeString = $this->parseCustomRoutes($requestCustom, $routes);
        if (empty($routeString)) {
            return $this->setting('default_route', [null, null, null]);
        }
        
        $controller = '';
        $action = '';
        $args = [];
        
        $parts = explode('/', $routeString, 3);
        if (!empty($parts['0'])) {
            $controller = $parts[0];
        }
        if (!empty($parts['1'])) {
            $action = $parts[1];
        }
        if (!empty($parts['2'])) {
            $args = explode('/', $parts[2]);
        }
        return [$controller, $action, $args];
    }
    
    final private function parseCustomRoutes($requestCustom, $routes)
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
                    return $v;
                }
            }
        }
        return $requestCustom;
    }
}