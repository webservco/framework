<?php

namespace WebServCo\Framework\Traits;

trait RequestUrlTrait
{
    abstract public function getHost();
    abstract public function getSchema();

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
        if (\WebServCo\Framework\Framework::isCli()) {
            return false;
        }
        $url = sprintf(
            '%s://%s%s',
            $this->getSchema(),
            $this->getHost(),
            $this->path
        );
        return rtrim($url, '/') . '/';
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
        if (!is_array($removeParameters)) {
            throw new \InvalidArgumentException('Agument must be an array.');
        }

        $url = $this->getShortUrl();
        $query = $this->getQuery();
        foreach ($removeParameters as $item) {
            if (array_key_exists($item, $query)) {
                unset($query[$item]);
            }
        }
        $url .= \WebServCo\Framework\Utils\Arrays::toUrlQueryString($query);
        return $url;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function query($key, $defaultValue = false)
    {
        return \WebServCo\Framework\ArrayStorage::get(
            $this->query,
            $key,
            $defaultValue
        );
    }
}
