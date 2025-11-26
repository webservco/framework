<?php

declare(strict_types=1);

namespace WebServCo\Framework;

use WebServCo\Framework\Interfaces\OutputLoaderInterface;
use WebServCo\Framework\Traits\ExposeLibrariesTrait;
use WebServCo\Framework\Traits\OutputTrait;
use WebServCo\Framework\Traits\ResponseUrlTrait;

abstract class AbstractRepository extends AbstractLibrary
{
    use OutputTrait;
    use ExposeLibrariesTrait;
    use ResponseUrlTrait;

    public function __construct(OutputLoaderInterface $outputLoader)
    {
        parent::__construct();

        $this->setOutputLoader($outputLoader);
    }
}
