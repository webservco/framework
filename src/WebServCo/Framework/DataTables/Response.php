<?php

declare(strict_types=1);

namespace WebServCo\Framework\DataTables;

use WebServCo\Framework\Interfaces\JsonInterface;

use function json_encode;

final class Response implements JsonInterface
{
    /**
     * Data.
     *
     * @var array<int,array<int|string,mixed>> $data
     */
    protected array $data;

    /**
    * @param array<int,array<int|string,mixed>> $data
    */
    public function __construct(
        protected int $draw,
        protected int $recordsTotal,
        protected int $recordsFiltered,
        array $data = [],
    ) {
        $this->data = $data;
    }

    /**
    * @return array<string,mixed>
    */
    public function toArray(): array
    {
        $array = [
            'data' => [],
            'draw' => $this->draw,
            'recordsFiltered' => $this->recordsFiltered,
            'recordsTotal' => $this->recordsTotal,
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
