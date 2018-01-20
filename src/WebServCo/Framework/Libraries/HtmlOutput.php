<?php
namespace WebServCo\Framework\Libraries;

final class HtmlOutput extends \WebServCo\Framework\AbstractLibrary implements
    \WebServCo\Framework\Interfaces\OutputInterface
{
    private $path;
    private $template;
    
    final public function __construct($config)
    {
        parent::__construct($config);
    }
    
    final public function setPath($path)
    {
        $this->path = $path;
        return true;
    }
    
    final public function setTemplate($template)
    {
        $this->template = $template;
        return true;
    }
    
    final public function render()
    {
        try {
            $templatePath = "{$this->path}{$this->template}.php";
            if (!is_file($templatePath)) {
                throw new \ErrorException('Template file not found');
            }
            ob_start();
            include $templatePath;
            $output = ob_get_clean();
        } catch (\Throwable $e) { //php > 7
            ob_end_clean();
            throw $e;
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
        return $output;
    }
}
