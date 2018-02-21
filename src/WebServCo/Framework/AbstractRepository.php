<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Framework as Fw;

abstract class AbstractRepository
{
    use \WebServCo\Framework\Traits\OutputTrait;
    use \WebServCo\Framework\Traits\ExposeLibrariesTrait;
    use \WebServCo\Framework\Traits\ResponseUrlTrait;
    
    public function __construct(\WebServCo\Framework\AbstractOutputLoader $outputLoader)
    {
        $this->setOutputLoader($outputLoader);
    }
}
