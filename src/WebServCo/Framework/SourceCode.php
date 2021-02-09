<?php declare(strict_types = 1);

namespace WebServCo\Framework;

final class SourceCode
{

    const TYPE_XML = 'XML';

    protected string $type;
    protected string $data;

    public function __construct(string $type, string $data)
    {
        switch ($type) {
            case self::TYPE_XML:
                break;
            default:
                throw new \WebServCo\Framework\Exceptions\NotImplementedException('Type not implemented.');
        }
        $this->type = $type;
        $this->data = $data;
    }

    public function highlight(): string
    {
        switch ($this->type) {
            case self::TYPE_XML:
                return $this->highlightXml($this->data);
            default:
                throw new \WebServCo\Framework\Exceptions\NotImplementedException('Type not implemented.');
        }
    }

    protected function highlightXml(string $data): string
    {
        $data = \htmlentities($data);
        $data = \str_replace('&lt;', '<span style="color: purple">&lt;', $data);
        $data = \str_replace('&gt;', '&gt;</span>', $data);
        return $data;
    }
}
