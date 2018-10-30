<?php
namespace WebServCo\Framework;

abstract class AbstractRequest extends \WebServCo\Framework\AbstractLibrary
{
    /**
     * Sanitized _SERVER data.
     */
    protected $server = [];
    /**
     * Request method.
     */
    protected $method;
    /**
     * Current script filename. Should most commonly be index.php
     */
    protected $filename;
    /**
     * Script path.
     * For HTTP requests this will be public web server subdirectory
     * the project is located in.
     * For CLI request this will be the script path
     * (full or relative, depending on how the script was called).
     */
    protected $path = '';
    /**
     * Sanitized Framework customized target path.
     */
    protected $target = '';
    /**
     * Sanitized request query.
     */
    protected $query = [];
    /**
     * Sanitized Framework customized CLI arguments.
     *
     * Excludes the script name and the second argument
     * which is the Framework customized target path.
     */
    protected $args = [];
    /**
     * Dummy file extension used in the URL
     */
    protected $suffix;
    /**
     * Request body raw data.
     */
    protected $body;
}
