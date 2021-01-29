<?php
namespace WebServCo\Framework\DataTables;

abstract class AbstractDataTables implements \WebServCo\Framework\Interfaces\DataTablesInterface
{
    /**
    * @param Request $request
    * @return array<int,array<string,float|int|string>>
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
            $this->getData($request)
        );
    }
}
