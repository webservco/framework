<?php

declare(strict_types=1);

namespace WebServCo\Framework;

use WebServCo\Framework\Exceptions\NotImplementedException;

use function htmlentities;
use function str_replace;

final class SourceCode
{
    public const string TYPE_XML = 'XML';

    public function __construct(protected string $type, protected string $data)
    {
        switch ($type) {
            case self::TYPE_XML:
                break;
            default:
                throw new NotImplementedException('Type not implemented.');
        }
    }

    public function highlight(): string
    {
        switch ($this->type) {
            case self::TYPE_XML:
                return $this->highlightXml($this->data);
            default:
                throw new NotImplementedException('Type not implemented.');
        }
    }

    protected function highlightXml(string $data): string
    {
        $data = htmlentities($data);
        $data = str_replace('&lt;', '<span style="color: purple">&lt;', $data);
        $data = str_replace('&gt;', '&gt;</span>', $data);

        return $data;
    }
}
