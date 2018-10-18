<?php
namespace WebServCo\Framework\Traits;

trait ResponseUrlTrait
{
    abstract protected function request();

    /**
     * Redirect to an application location (Request target).
     * This method returns a HttpResponse object that needs to be in turn returned to the application.
     */
    final protected function getRedirectResponse($location, $addSuffix = true)
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
     * This method returns a HttpResponse object that needs to be in turn returned to the application.
     */
    final protected function getReloadResponse($removeParameters = [])
    {
        $url = $this->request()->getUrl($removeParameters);
        return $this->getRedirectUrlResponse($url);
    }

    /**
     * Redirect to a full URL.
     * This method returns a HttpResponse object that needs to be in turn returned to the application.
     */
    final protected function getRedirectUrlResponse($url)
    {
        return new \WebServCo\Framework\HttpResponse(
            null,
            302,
            ['Location' => $url]
        );
    }
}
