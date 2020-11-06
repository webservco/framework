<?php
namespace WebServCo\Framework\Traits;

use WebServCo\Framework\Http\Response;

trait OutputTrait
{
    protected $outputCode;
    protected $outputLoader;

    final protected function setOutputLoader(\WebServCo\Framework\AbstractOutputLoader $outputLoader)
    {
        $this->outputCode = 200; // default
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

    protected function outputHtmlPartial($data, $template)
    {
        return new Response(
            $this->output()->html($data, $template),
            $this->outputCode,
            ['Content-Type' => 'text/html']
        );
    }

    protected function outputHtml($data, $pageTemplate, $mainTemplate = null)
    {
        return new Response(
            $this->output()->htmlPage($data, $pageTemplate, $mainTemplate),
            $this->outputCode,
            ['Content-Type' => 'text/html']
        );
    }

    protected function outputJson($content, $result = true)
    {
        $data = [
            'result' => $result,
            'data' => $content,
        ];
        return new Response(
            $this->output()->json($data),
            $this->outputCode,
            ['Content-Type' => 'application/json']
        );
    }

    protected function setOutputCode($outputCode)
    {
        $this->outputCode = $outputCode;
    }
}
