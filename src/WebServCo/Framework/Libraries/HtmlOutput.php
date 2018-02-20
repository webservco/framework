<?php
namespace WebServCo\Framework\Libraries;

final class HtmlOutput extends \WebServCo\Framework\AbstractLibrary implements
    \WebServCo\Framework\Interfaces\OutputInterface
{
    private $path;
    private $template;
    
    public function setPath($path)
    {
        $this->path = $path;
        return true;
    }
    
    public function setTemplate($template)
    {
        $this->template = $template;
        return true;
    }
    
    public function render()
    {
        try {
            $templatePath = "{$this->path}{$this->template}.php";
            if (!is_file($templatePath)) {
                throw new \ErrorException('Template file not found');
            }
            ob_start();
            include $templatePath;
            $output = ob_get_clean();
        } catch (\Throwable $e) { // php7
            ob_end_clean();
            throw $e;
        } catch (\Exception $e) { // php5
            ob_end_clean();
            throw $e;
        }
        return $output;
    }
}
