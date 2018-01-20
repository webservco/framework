<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Framework as Fw; //XXX

class AbstractOutputLoader
{
    protected $projectPath;
    
    public function __construct($projectPath)
    {
        $this->projectPath = $projectPath;
    }
    
    private function htmlOutput()
    {
        return Fw::getLibrary('HtmlOutput');
    }
    
    private function jsonOutput()
    {
        return Fw::getLibrary('JsonOutput');
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
