<?php
namespace WebServCo\Framework\Utils;

final class Template
{
    public static function render($templatePath, $templateName, $data)
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
