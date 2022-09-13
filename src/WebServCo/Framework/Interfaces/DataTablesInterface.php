<?php

namespace WebServCo\Framework\Interfaces;

interface DataTablesInterface
{
    public function getResponse(\WebServCo\Framework\DataTables\Request $request);
}
