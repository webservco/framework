<?php declare(strict_types = 1);

namespace WebServCo\Framework;

abstract class AbstractRepository extends \WebServCo\Framework\AbstractLibrary
{

    use \WebServCo\Framework\Traits\OutputTrait;
    use \WebServCo\Framework\Traits\ExposeLibrariesTrait;
    use \WebServCo\Framework\Traits\ResponseUrlTrait;

    public function __construct(\WebServCo\Framework\Interfaces\OutputLoaderInterface $outputLoader)
    {
        parent::__construct();

        $this->setOutputLoader($outputLoader);
    }
}
