<?php
namespace WebServCo\Framework\Traits;

trait RequestUrlTrait
{
    public function getSuffix()
    {
        return $this->suffix;
    }
    
    public function getTarget()
    {
        return $this->target;
    }
    
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
    
    public function getShortUrl()
    {
        $url = $this->getAppUrl();
        $url .= $this->getTarget();
        $url .= $this->getSuffix();
        return $url;
    }
    
    public function getUrl($removeParameters = [])
    {
        $url = $this->getShortUrl();
        $query = $this->getQuery();
        foreach ($removeParameters as $item) {
            if (array_key_exists($item, $query)) {
                unset($query[$item]);
            }
        }
        $url .= $this->queryToString($query);
        return $url;
    }
    
    public function getQuery()
    {
        return $this->query;
    }
    
    public function queryToString($query = [])
    {
        if (empty($query)) {
            return false;
        }
        $queries = [];
        foreach ($query as $k => $v) {
            $queries[] = sprintf('%s=%s', $k, $v);
        }
        return '?' . implode('&', $queries);
    }
}
