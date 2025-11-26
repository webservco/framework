<?php

declare(strict_types=1);

namespace WebServCo\Framework\Traits;

use WebServCo\Framework\Http\Response;
use WebServCo\Framework\Interfaces\RequestInterface;

trait ResponseUrlTrait
{
    /**
     * Redirect to an application location (Request target).
     * This method returns a Response object that needs to be in turn returned to the application.
     */
    final protected function getRedirectResponse(string $location, bool $addSuffix = true): Response
    {
        $url = $this->request()->getAppUrl();
        $url .= $location;
        if ($addSuffix) {
            $url .= $this->request()->getSuffix();
        }

        return $this->getRedirectUrlResponse($url);
    }

    /**
     * Redirect to the current URL.
     * This method returns a Response object that needs to be in turn returned to the application.
     *
     * @param array<int,string> $removeParameters
     */
    final protected function getReloadResponse(array $removeParameters = []): Response
    {
        $url = $this->request()->getUrl($removeParameters);

        return $this->getRedirectUrlResponse($url);
    }

    /**
     * Redirect to a full URL.
     * This method returns a Response object that needs to be in turn returned to the application.
     *
     * "303 See Other (since HTTP/1.1)
     * The response to the request can be found under another URI using the GET method.
     * When received in response to a POST (or PUT/DELETE), the client should presume that the server has received
     * the data and should issue a new GET request to the given URI."
     */
    final protected function getRedirectUrlResponse(string $url, int $statusCode = 303): Response
    {
        return new Response(
            '',
            $statusCode,
            ['Location' => [$url]],
        );
    }

    abstract protected function request(): RequestInterface;
}
