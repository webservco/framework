<?php
namespace WebServCo\Framework\Libraries;

final class Request extends \WebServCo\Framework\AbstractLibrary
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
    
    use \WebServCo\Framework\Traits\RequestProcessTrait;
    use \WebServCo\Framework\Traits\RequestServerTrait;
    use \WebServCo\Framework\Traits\RequestUrlTrait;
     
    public function __construct($config, $server, $post = [])
    {
        parent::__construct($config);
        
        $this->init($server, $post);
    }
    
    public function getQuery()
    {
        return $this->query;
    }
    
    public function getTarget()
    {
        return $this->target;
    }
    
    public function getArgs()
    {
        return $this->args;
    }
    
    public function getSuffix()
    {
        return $this->suffix;
    }
}
