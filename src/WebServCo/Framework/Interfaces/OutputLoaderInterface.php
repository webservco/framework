<?php declare(strict_types = 1);

namespace WebServCo\Framework\Interfaces;

interface OutputLoaderInterface
{
    public function cli(string $string, bool $eol = true): bool;
    
    /**
    * @param array<int|string,mixed> $data
    * @param string $template
    * @return string
    */
    public function html(array $data, string $template): string;

    /**
    * @param array<int|string,mixed> $data
    * @param string $pageTemplate
    * @param string $mainTemplate
    * @return string
    */
    public function htmlPage(array $data, string $pageTemplate, string $mainTemplate = null): string;

    /**
    * @param array<string,mixed> $data
    */
    public function json(array $data): string;
}
