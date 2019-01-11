<?php
namespace WebServCo\Framework\DataTables;

class Response implements \WebServCo\Framework\Interfaces\JsonInterface
{
    protected $draw;

    protected $recordsTotal;

    protected $recordsFiltered;

    protected $data;

    public function __construct($draw, $recordsTotal, $recordsFiltered, $data = [])
    {
        $this->draw = $draw;
        $this->recordsTotal = $recordsTotal;
        $this->recordsFiltered = $recordsFiltered;
        $this->data = $data;
    }

    public function toArray()
    {
        $array = [
            'draw' => $this->draw,
            'recordsTotal' => $this->recordsTotal,
            'recordsFiltered' => $this->recordsFiltered,
            'data' => []
        ];
        foreach ($this->data as $item) {
            $array['data'][] = $item;
        }
        return $array;
    }

    public function toJson()
    {
        $array = $this->toArray();
        return json_encode($array);
    }
}
