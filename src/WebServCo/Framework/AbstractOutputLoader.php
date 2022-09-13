<?php

namespace WebServCo\Framework;

abstract class AbstractOutputLoader
{
    protected $projectPath;
    protected $htmlOutput;
    protected $jsonOutput;

    public function __construct(
        $projectPath,
        \WebServCo\Framework\Libraries\HtmlOutput $htmlOutput = null,
        \WebServCo\Framework\Libraries\JsonOutput $jsonOutput = null
    ) {
        $this->projectPath = $projectPath;
        $this->htmlOutput = $htmlOutput;
        $this->jsonOutput = $jsonOutput;
    }

    public function htmlOutput()
    {
        return $this->htmlOutput;
    }

    public function jsonOutput()
    {
        return $this->jsonOutput;
    }

    private function getRenderedHtml($template)
    {
        /**
         * Set template path.
         */
        $this->htmlOutput()->setPath("{$this->projectPath}resources/views/");
        /**
         * Set page template
         */
        $this->htmlOutput()->setTemplate($template);
        return $this->htmlOutput()->render();
    }

    protected function setHtmlTemplateData($data)
    {
        if (!is_array($data)) {
            return false;
        }
        foreach ($data as $key => $value) {
            $this->htmlOutput()->setData($key, $value);
        }
        return true;
    }

    public function html($data, $template)
    {
        $this->setHtmlTemplateData($data);
        return $this->getRenderedHtml($template);
    }

    public function htmlPage($data, $pageTemplate, $mainTemplate = null)
    {
        /**
         * Set template data.
         */
        $this->setHtmlTemplateData($data);

        /**
         * Partials
         */
        foreach ($this->htmlOutput()->setting('partials', []) as $partialTemplate) {
            $this->htmlOutput()->setData(
                "tpl_{$partialTemplate}",
                $this->getRenderedHtml($partialTemplate)
            );
        }
        /**
         * Page content
         */
        $this->htmlOutput()->setData(
            'tpl_content',
            $this->getRenderedHtml($pageTemplate)
        );
        /**
         * Main template
         */
        $mainTemplate = $mainTemplate ? $mainTemplate :
            $this->htmlOutput()->setting('main_template', 'layout');
        return $this->getRenderedHtml($mainTemplate);
    }

    public function json($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $this->jsonOutput()->setData($key, $value);
            }
        }
        return $this->jsonOutput()->render();
    }

    public function cli($string, $eol = true)
    {
        echo $string;
        if ($eol) {
            echo PHP_EOL;
        }
        return true;
    }
}
