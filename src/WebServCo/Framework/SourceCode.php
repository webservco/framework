<?php
namespace WebServCo\Framework;

final class SourceCode
{
    const TYPE_XML = 'XML';

    protected $type;
    protected $data;

    public function __construct($type, $data)
    {
        switch ($type) {
            case self::TYPE_XML:
                break;
            default:
                throw new \WebServCo\Framework\Exceptions\ApplicationException('Type not implemented');
                break;
        }
        $this->type = $type;
        $this->data = $data;
    }

    public function highlight()
    {
        switch ($this->type) {
            case self::TYPE_XML:
                return $this->highlightXml($this->data);
                break;
            default:
                return false;
                break;
        }
    }

    protected function highlightXml($data)
    {
        $data = htmlentities($data);
        $data = str_replace('&lt;', '<span style="color: purple">&lt;', $data);
        $data = str_replace('&gt;', '&gt;</span>', $data);
        return $data;
    }
}
