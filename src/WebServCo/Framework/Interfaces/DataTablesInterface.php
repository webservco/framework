<?php

declare(strict_types=1);

namespace WebServCo\Framework\Interfaces;

use WebServCo\Framework\DataTables\Request;
use WebServCo\Framework\DataTables\Response;

interface DataTablesInterface
{
    public function getResponse(Request $request,): Response;
}
