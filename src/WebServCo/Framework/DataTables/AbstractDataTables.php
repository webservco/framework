<?php

declare(strict_types=1);

namespace WebServCo\Framework\DataTables;

use WebServCo\Framework\Interfaces\DataTablesInterface;

abstract class AbstractDataTables implements DataTablesInterface
{
    /**
    * @return array<int,array<string,float|int|string|null>>
    */
    abstract protected function getData(Request $request): array;

    abstract protected function getRecordsFiltered(): int;

    abstract protected function getRecordsTotal(): int;

    public function getResponse(Request $request): Response
    {
        return new Response(
            $request->getDraw(),
            $this->getRecordsTotal(),
            $this->getRecordsFiltered(),
            $this->getData($request),
        );
    }
}
