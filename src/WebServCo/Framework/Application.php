<?php

declare(strict_types=1);

namespace WebServCo\Framework;

use Throwable;
use WebServCo\Framework\Environment\Config;
use WebServCo\Framework\Helpers\ErrorMessageHelper;
use WebServCo\Framework\Interfaces\ResponseInterface;
use WebServCo\Framework\Values\Environment;

use function basename;
use function sprintf;

use const PHP_EOL;

/**
* An example Application implementation.
*
* Can be replaced or extended by consumer implementations.
* Custom functionality in this class: use custom output.
*/
// @phpcs:ignore SlevomatCodingStandard.Classes.RequireAbstractOrFinal.ClassNeitherAbstractNorFinal
class Application extends AbstractApplication
{
    /**
    * CLI message to output in case of error.
    */
    protected function getCliOutput(Throwable $throwable): string
    {
        $output = 'Boo boo' . PHP_EOL;
        $output .= ErrorMessageHelper::format($throwable);
        $output .= PHP_EOL;

        return $output;
    }

    /**
    * HTML code to output in case of error.
    */
    protected function getHttpOutput(Throwable $throwable): string
    {
        $output = '<!doctype html>
            <html>
            <head>
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Oops</title>
                <style>
                * {background: #f2dede; color: #a94442; overflow-wrap: break-word;}
                .i {margin-left: auto; margin-right: auto; text-align: center; width: auto;}
                small {font-size: 0.8em;}
                </style>
            </head>
            <body><div class="i"><br>';
        $output .= sprintf('<h1>%s</h1>', $throwable->getCode() === 404 ? 'Resource not found' : 'Boo boo');

        if (Config::string('APP_ENVIRONMENT') === Environment::DEVELOPMENT) {
            $output .= sprintf(
                '<p><i>%s</i></p><p>%s:%s</p>',
                $throwable->getMessage(),
                $throwable->getFile(),
                $throwable->getLine(),
            );
            $trace = $throwable->getTrace();
            if (!empty($trace)) {
                $output .= "<p>";
                $output .= "<small>";
                foreach ($trace as $item) {
                    if (!empty($item['class'])) {
                        $output .= sprintf('%s%s', $item['class'], $item['type']);
                        $output .= "";
                    }
                    if (!empty($item['function'])) {
                        $output .= sprintf('%s', $item['function']);
                        $output .= "";
                    }
                    if (!empty($item['file'])) {
                        $output .= sprintf(
                            ' [%s:%s]',
                            basename($item['file']),
                            $item['line'],
                        );
                        $output .= " ";
                    }
                    $output .= "<br>";
                }
                $output .= "</small></p>";
            }
        }

        $output .= '</div></body>';
        $output .= '</html>';

        return $output;
    }

    /**
    * Get Response.
    */
    protected function getResponse(): ResponseInterface
    {
        return $this->execute();
    }
}
