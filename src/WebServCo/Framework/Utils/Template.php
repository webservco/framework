<?php
namespace WebServCo\Framework\Utils;

final class Template
{
    /**
    * @param string $templatePath
    * @param string $templateName
    * @param array<mixed> $data
    * @return string
    */
    public static function render(string $templatePath, string $templateName, array $data) : string
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
