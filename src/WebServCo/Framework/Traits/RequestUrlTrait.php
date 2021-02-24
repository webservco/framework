<?php

declare(strict_types=1);

namespace WebServCo\Framework\Traits;

trait RequestUrlTrait
{

    abstract public function getHost(): string;

    abstract public function getSchema(): string;

    public function getSuffix(): string
    {
        return $this->suffix;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getAppUrl(): string
    {
        if (\WebServCo\Framework\Helpers\PhpHelper::isCli()) {
            return '';
        }
        $url = \sprintf(
            '%s://%s%s',
            $this->getSchema(),
            $this->getHost(),
            $this->path
        );
        return \rtrim($url, '/') . '/';
    }

    public function getShortUrl(): string
    {
        $url = $this->getAppUrl();
        if (!empty($url)) {
            $url .= $this->getTarget();
            $url .= $this->getSuffix();
        }
        return $url;
    }

    /**
    * @param array<int,string> $removeParameters
    */
    public function getUrl(array $removeParameters = []): string
    {
        if (!\is_array($removeParameters)) {
            throw new \InvalidArgumentException('Agument must be an array.');
        }

        $url = $this->getShortUrl();
        $query = $this->getQuery();
        foreach ($removeParameters as $item) {
            if (!\array_key_exists($item, $query)) {
                continue;
            }

            unset($query[$item]);
        }
        return $url . \WebServCo\Framework\Utils\Arrays::toUrlQueryString($query);
    }

    /**
    * @return array<string,mixed>
    */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @param mixed $key Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     * @param mixed $defaultValue
     * @return mixed
     */
    public function query($key, $defaultValue = false)
    {
        return \WebServCo\Framework\ArrayStorage::get($this->query, $key, $defaultValue);
    }
}
