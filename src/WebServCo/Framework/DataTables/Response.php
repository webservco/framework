<?php declare(strict_types = 1);

namespace WebServCo\Framework\DataTables;

class Response implements \WebServCo\Framework\Interfaces\JsonInterface
{
    protected int $draw;

    protected int $recordsTotal;

    protected int $recordsFiltered;

    /**
    * @var array<int,array<int|string,mixed>> $data
    */
    protected array $data;

    /**
    * @param int $draw
    * @param int $recordsTotal
    * @param int $recordsFiltered
    * @param array<int,array<int|string,mixed>> $data
    */
    public function __construct(int $draw, int $recordsTotal, int $recordsFiltered, array $data = [])
    {
        $this->draw = $draw;
        $this->recordsTotal = $recordsTotal;
        $this->recordsFiltered = $recordsFiltered;
        $this->data = $data;
    }

    /**
    * @return array<string,mixed>
    */
    public function toArray(): array
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

    public function toJson(): string
    {
        $array = $this->toArray();
        return (string) json_encode($array);
    }
}
