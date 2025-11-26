<?php

declare(strict_types=1);

namespace WebServCo\Framework\Libraries;

use WebServCo\Framework\AbstractLibrary;
use WebServCo\Framework\Interfaces\OutputInterface;

use function json_encode;

final class JsonOutput extends AbstractLibrary implements
    OutputInterface
{
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function setTemplate(string $template): bool
    {
        return false;
    }

    public function render(): string
    {
        return (string) json_encode($this->getData());
    }
}
