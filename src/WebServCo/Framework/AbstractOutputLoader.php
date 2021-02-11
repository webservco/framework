<?php declare(strict_types = 1);

namespace WebServCo\Framework;

use WebServCo\Framework\Libraries\HtmlOutput;
use WebServCo\Framework\Libraries\JsonOutput;

abstract class AbstractOutputLoader implements \WebServCo\Framework\Interfaces\OutputLoaderInterface
{

    protected string $projectPath;
    protected HtmlOutput $htmlOutput;
    protected JsonOutput $jsonOutput;

    public function __construct(string $projectPath, HtmlOutput $htmlOutput, JsonOutput $jsonOutput)
    {
        $this->projectPath = $projectPath;
        $this->htmlOutput = $htmlOutput;
        $this->jsonOutput = $jsonOutput;
    }

    public function cli(string $string, bool $eol = true): bool
    {
        echo $string;
        if ($eol) {
            echo \PHP_EOL;
        }
        return true;
    }

    /**
    * @param array<int|string,mixed> $data
    */
    public function html(array $data, string $template): string
    {
        $this->setHtmlTemplateData($data);
        return $this->getRenderedHtml($template);
    }

    public function htmlOutput(): HtmlOutput
    {
        return $this->htmlOutput;
    }

    /**
    * @param array<int|string,mixed> $data
    */
    public function htmlPage(array $data, string $pageTemplate, ?string $mainTemplate = null): string
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
        if (!\WebServCo\Framework\Utils\Strings::isEmpty($pageTemplate)) {
            $this->htmlOutput()->setData(
                'tpl_content',
                $this->getRenderedHtml($pageTemplate)
            );
        }

        /**
         * Main template
         */
        $mainTemplate ??= $this->htmlOutput()->setting('main_template', 'layout');
        return $this->getRenderedHtml($mainTemplate);
    }

    /**
    * @param array<string,mixed> $data
    */
    public function json(array $data): string
    {
        if (\is_array($data)) {
            foreach ($data as $key => $value) {
                $this->jsonOutput()->setData($key, $value);
            }
        }
        return $this->jsonOutput()->render();
    }

    public function jsonOutput(): JsonOutput
    {
        return $this->jsonOutput;
    }

    /**
    * @param array<int|string,mixed> $data
    */
    protected function setHtmlTemplateData(array $data): bool
    {
        if (!\is_array($data)) {
            return false;
        }
        foreach ($data as $key => $value) {
            $this->htmlOutput()->setData($key, $value);
        }
        return true;
    }

    private function getRenderedHtml(string $template): string
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
}
