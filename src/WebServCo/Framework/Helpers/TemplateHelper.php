<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use WebServCo\Framework\Libraries\HtmlOutput;

final class TemplateHelper
{
    /**
    * @param array<int|string,mixed> $data
    */
    public static function render(string $templatePath, string $templateName, array $data): string
    {
        $output = new HtmlOutput();
        foreach ($data as $k => $v) {
            $output->setData($k, $v);
        }
        $output->setPath($templatePath);

        $output->setTemplate($templateName);

        return $output->render();
    }
}
