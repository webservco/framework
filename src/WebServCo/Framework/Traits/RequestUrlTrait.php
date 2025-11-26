<?php

declare(strict_types=1);

namespace WebServCo\Framework\Traits;

use WebServCo\Framework\ArrayStorage;
use WebServCo\Framework\Helpers\ArrayHelper;
use WebServCo\Framework\Helpers\PhpHelper;

use function array_key_exists;
use function rtrim;
use function sprintf;

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
        if (PhpHelper::isCli()) {
            return '';
        }
        $url = sprintf(
            '%s://%s%s',
            $this->getSchema(),
            $this->getHost(),
            $this->path,
        );

        return rtrim($url, '/') . '/';
    }

    public function getShortUrl(): string
    {
        $url = $this->getAppUrl();
        if ($url) {
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
        $url = $this->getShortUrl();
        $query = $this->getQuery();
        foreach ($removeParameters as $item) {
            if (!array_key_exists($item, $query)) {
                continue;
            }

            unset($query[$item]);
        }

        return $url . ArrayHelper::toUrlQueryString($query);
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
     *                          (eg 'i18n/lang').
     */
    public function query(mixed $key, mixed $defaultValue = null): mixed
    {
        return ArrayStorage::get($this->query, $key, $defaultValue);
    }
}
