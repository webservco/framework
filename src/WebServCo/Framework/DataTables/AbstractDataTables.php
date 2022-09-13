<?php

namespace WebServCo\Framework\DataTables;

abstract class AbstractDataTables implements \WebServCo\Framework\Interfaces\DataTablesInterface
{
    abstract protected function getData(Request $request);
    abstract protected function getRecordsFiltered();
    abstract protected function getRecordsTotal();

    public function getResponse(Request $request)
    {
        return new Response(
            $request->getDraw(),
            $this->getRecordsTotal(),
            $this->getRecordsFiltered(),
            $this->getData($request)
        );
    }
}
