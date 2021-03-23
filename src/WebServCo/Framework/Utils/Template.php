<?php

declare(strict_types=1);

namespace WebServCo\Framework\Utils;

final class Template
{

    /**
    * @param array<int|string,mixed> $data
    */
    public static function render(string $templatePath, string $templateName, array $data): string
    {
        $output = new \WebServCo\Framework\Libraries\HtmlOutput();
        foreach ($data as $k => $v) {
            $output->setData($k, $v);
        }
        $output->setPath($templatePath);

        $output->setTemplate($templateName);
        return $output->render();
    }
}
