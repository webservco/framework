<?php

declare(strict_types=1);

namespace WebServCo\Framework\Libraries;

final class JsonOutput extends \WebServCo\Framework\AbstractLibrary implements
    \WebServCo\Framework\Interfaces\OutputInterface
{

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function setTemplate(string $template): bool
    {
        return false;
    }

    public function render(): string
    {
        return (string) \json_encode($this->getData());
    }
}
