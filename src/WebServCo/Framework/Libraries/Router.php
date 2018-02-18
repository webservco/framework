<?php
namespace WebServCo\Framework\Libraries;

final class Router extends \WebServCo\Framework\AbstractLibrary
{
    public function getRoute($requestCustom, $routes, $extraArgs = [])
    {
        $routeString = $this->parseCustomRoutes($requestCustom, $routes);
        if (empty($routeString) || 'index' == $routeString) {
            $defaultRoute = $this->setting('default_route');
            if (!isset($defaultRoute[1])) {
                throw new \ErrorException("Default route missing or not valid");
            }
            return $defaultRoute;
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
        if (!empty($extraArgs)) {
            foreach ($extraArgs as $k => $v) {
                $args[] = $v;
            }
        }
        return [$controller, $action, $args];
    }
    
    private function parseCustomRoutes($requestCustom, $routes)
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
