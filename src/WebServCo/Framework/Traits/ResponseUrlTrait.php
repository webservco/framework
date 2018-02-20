<?php
namespace WebServCo\Framework\Traits;

trait ResponseUrlTrait
{
    abstract protected function request();
    
    /**
     * Redirect to an application location (Request target).
     */
    final protected function redirect($location, $addSuffix = true)
    {
        $url = $this->request()->getAppUrl();
        $url .= $location;
        if ($addSuffix) {
            $url .= $this->request()->getSuffix();
        }
        return $this->redirectUrl($url);
    }
    
    /**
     * Redirect to the current URL
     */
    final protected function reload($removeParameters = [])
    {
        $url = $this->request()->getUrl($removeParameters);
        return $this->redirectUrl($url);
    }
    
    /**
     * Redirect to a full URL
     */
    final protected function redirectUrl($url)
    {
        return new \WebServCo\Framework\Libraries\HttpResponse(
            null,
            302,
            ['Location' => $url]
        );
    }
}
