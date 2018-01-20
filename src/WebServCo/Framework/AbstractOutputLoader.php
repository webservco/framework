<?php
namespace WebServCo\Framework;

class AbstractOutputLoader
{
    protected $projectPath;
    protected $htmlOutput;
    protected $jsonOutput;
    
    public function __construct($projectPath, $htmlOutput = null, $jsonOutput = null)
    {
        $this->projectPath = $projectPath;
        $this->htmlOutput = $htmlOutput;
        $this->jsonOutput = $jsonOutput;
    }
    
    protected function htmlOutput()
    {
        return $this->htmlOutput;
    }
    
    protected function jsonOutput()
    {
        $this->jsonOutput = $jsonOutput;
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
    
    public function html($data, $pageTemplate, $mainTemplate = null)
    {
        /**
         * Set page data
         */
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $this->htmlOutput()->setData($key, $value);
            }
        }
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
}
