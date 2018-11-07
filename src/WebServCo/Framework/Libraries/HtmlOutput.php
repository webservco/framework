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
        ob_start();
        try {
            $templatePath = "{$this->path}{$this->template}.php";
            if (!is_file($templatePath)) {
                throw new \WebServCo\Framework\Exceptions\ApplicationException(
                    sprintf('Template "%s" not found', $this->template)
                );
            }
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
