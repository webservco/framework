<?php

namespace WebServCo\Framework;

abstract class AbstractController extends \WebServCo\Framework\AbstractLibrary
{
    use \WebServCo\Framework\Traits\OutputTrait;
    use \WebServCo\Framework\Traits\ExposeLibrariesTrait;
    use \WebServCo\Framework\Traits\ResponseUrlTrait;

    public function __construct(\WebServCo\Framework\AbstractOutputLoader $outputLoader)
    {
        parent::__construct();
        $this->setOutputLoader($outputLoader);
    }
}
