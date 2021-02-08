<?php declare(strict_types = 1);

namespace WebServCo\Framework;

abstract class AbstractRequest extends \WebServCo\Framework\AbstractLibrary
{
    /**
     * Sanitized _SERVER data.
     * @var array<string,mixed>
     */
    protected array $server = [];

    /**
     * Request method.
     */
    protected string $method = '';

    /**
     * Current script filename. Should most commonly be index.php
     */
    protected string $filename;

    /**
     * Script path.
     * For HTTP requests this will be public web server subdirectory
     * the project is located in.
     * For CLI request this will be the script path
     * (full or relative, depending on how the script was called).
     */
    protected string $path = '';

    /**
     * Sanitized Framework customized target path.
     */
    protected string $target = '';

    /**
     * Sanitized request query.
     *
     * @var array<mixed>
     */
    protected array $query = [];

    /**
     * Sanitized Framework customized CLI arguments.
     *
     * Excludes the script name and the second argument
     * which is the Framework customized target path.
     *
     * @var array<int,string>
     */
    protected array $args = [];

    /**
     * Dummy file extension used in the URL
     */
    protected string $suffix;

    /**
     * Request body raw data.
     */
    protected string $body;
}
