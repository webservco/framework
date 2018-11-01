<?php
namespace WebServCo\Framework\Traits;

trait OutputTrait
{
    protected $outputLoader;

    final protected function setOutputLoader(\WebServCo\Framework\AbstractOutputLoader $outputLoader)
    {
        $this->outputLoader = $outputLoader;
    }

    final protected function output()
    {
        return $this->outputLoader;
    }

    final protected function outputCli($string = '', $eol = true)
    {
        return $this->output()->cli($string, $eol);
    }

    protected function outputHtml($data, $pageTemplate, $mainTemplate = null)
    {
        return new \WebServCo\Framework\HttpResponse(
            $this->output()->htmlPage($data, $pageTemplate, $mainTemplate),
            200,
            ['Content-Type' => 'text/html']
        );
    }

    protected function outputJson($content, $result = true)
    {
        $data = [
            'result' => $result,
            'data' => $content,
        ];
        return new \WebServCo\Framework\HttpResponse(
            $this->output()->json($data),
            200,
            ['Content-Type' => 'application/json']
        );
    }
}
