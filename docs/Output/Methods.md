# Output methods

> Available in each controller

## `output()`

Direct access to the Output Loader object.

## `outputCli($string = '', $eol = true)`

Echoes the string and optional `PHP_EOL`.

## `outputHtmlPartial($data, $template)`

Returns `\WebServCo\Framework\Http\Response`.

## `outputHtml($data, $pageTemplate, $mainTemplate = null)`

Returns `\WebServCo\Framework\Http\Response`.

## `outputJson($content, $result = true)`

Returns `\WebServCo\Framework\Http\Response`.
