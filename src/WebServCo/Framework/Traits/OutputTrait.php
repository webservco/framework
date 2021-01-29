<?php

declare(strict_types=1);

namespace WebServCo\Framework\Traits;

use WebServCo\Framework\Http\Response;
use WebServCo\Framework\Interfaces\OutputLoaderInterface;

trait OutputTrait
{
    protected int $outputCode;
    protected OutputLoaderInterface $outputLoader;

    final protected function setOutputLoader(OutputLoaderInterface $outputLoader): bool
    {
        $this->outputCode = 200; // default
        $this->outputLoader = $outputLoader;
        return true;
    }

    final protected function output(): OutputLoaderInterface
    {
        return $this->outputLoader;
    }

    final protected function outputCli(string $string = '', bool $eol = true): bool
    {
        return $this->output()->cli($string, $eol);
    }

    /**
    * @param array<int|string,mixed> $data
    * @param string $template
    * @return Response
    */
    protected function outputHtmlPartial(array $data, string $template): Response
    {
        return new Response(
            $this->output()->html($data, $template),
            $this->outputCode,
            ['Content-Type' => ['text/html']]
        );
    }

    /**
    * @param array<int|string,mixed> $data
    * @param string $pageTemplate
    * @param string $mainTemplate
    * @return Response
    */
    protected function outputHtml(array $data, string $pageTemplate, string $mainTemplate = null): Response
    {
        return new Response(
            $this->output()->htmlPage($data, $pageTemplate, $mainTemplate),
            $this->outputCode,
            ['Content-Type' => ['text/html']]
        );
    }

    /**
    * @param array<int|string,mixed> $content
    * @param bool $result
    * @return Response
    */
    protected function outputJson(array $content, bool $result = true): Response
    {
        $data = [
            'result' => $result,
            'data' => $content,
        ];
        return new Response(
            $this->output()->json($data),
            $this->outputCode,
            ['Content-Type' => ['application/json']]
        );
    }

    protected function setOutputCode(int $outputCode): bool
    {
        $this->outputCode = $outputCode;
        return true;
    }
}
