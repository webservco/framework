<?php
namespace WebServCo\Framework\Traits;

trait OutputTrait
{
    protected $outputLoader;
    
    final protected function output()
    {
        return $this->outputLoader;
    }
    
    final protected function echo($string, $eol = true)
    {
        return $this->output()->write($string, $eol);
    }
    
    protected function outputHtml($data, $pageTemplate, $mainTemplate = null)
    {
        return new \WebServCo\Framework\Libraries\HttpResponse(
            $this->output()->htmlPage($data, $pageTemplate, $mainTemplate),
            200,
            ['Content-Type' => 'text/html']
        );
    }
    
    protected function outputJson($data)
    {
        return new \WebServCo\Framework\Libraries\HttpResponse(
            $this->output()->json($data),
            200,
            ['Content-Type' => 'application/json']
        );
    }
}
