<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Framework as Fw;

class OutputLoader
{
    private $pathProject;
    
    final public function __construct($pathProject)
    {
        $this->pathProject = $pathProject;
    }
    
    private function getRenderedHtml($template)
    {
        /**
         * Set template path.
         */
        Fw::output('html')->setPath("{$this->pathProject}resources/views/");
        /**
         * Set page template
         */
        Fw::output('html')->setTemplate($template);
        return Fw::output('html')->render();
    }
    
    public function html($data, $pageTemplate, $mainTemplate = null)
    {
        /**
         * Set page data
         */
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                Fw::output('html')->setData($key, $value);
            }
        }
        /**
         * Partials
         */
        foreach (Fw::output('html')->setting('partials', []) as $partialTemplate) {
            Fw::output('html')->setData(
                "tpl_{$partialTemplate}",
                $this->getRenderedHtml($partialTemplate)
            );
        }
        /**
         * Page content
         */
        Fw::output('html')->setData(
            'tpl_content',
            $this->getRenderedHtml($pageTemplate)
        );
        /**
         * Main template
         */
        $mainTemplate = $mainTemplate ? $mainTemplate :
            Fw::output('html')->setting('main_template', 'layout');
        return $this->getRenderedHtml($mainTemplate);
    }
    
    public function json($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                Fw::output('json')->setData($key, $value);
            }
        }
        return Fw::output('json')->render();
    }
}
