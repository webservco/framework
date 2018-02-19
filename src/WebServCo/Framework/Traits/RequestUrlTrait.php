<?php
namespace WebServCo\Framework\Traits;

trait RequestUrlTrait
{
    public function getAppUrl()
    {
        if (\WebServCo\Framework\Framework::isCLI()) {
            return false;
        }
        return $this->getSchema() .
        '://' .
        $this->getHost() .
        $this->path .
        DIRECTORY_SEPARATOR;
    }
    
    public function getUrl($includeQuery = true)
    {
        $url = $this->getAppUrl();
        $url .= $this->getTarget();
        $url .= $this->getSuffix();
        if ($includeQuery) {
            $url .= '?' . $this->getQueryString();
        }
        return $url;
    }
    
    public function getQueryString()
    {
        $queries = [];
        $query = $this->getQuery();
        foreach ($query as $k => $v) {
            $queries[] = sprintf('%s=%s', $k, $v);
        }
        return implode('&', $queries);
    }
}
