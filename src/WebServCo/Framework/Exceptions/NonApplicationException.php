<?php

declare(strict_types=1);

namespace WebServCo\Framework\Exceptions;

/**
* An exception that happens outside the usual app run.
*
* It means it can not be handled by the app because it doesn't have access to it.
*/
// @phpcs:ignore SlevomatCodingStandard.Classes.RequireAbstractOrFinal.ClassNeitherAbstractNorFinal
class NonApplicationException extends ApplicationException
{
}
