<?php
namespace WebServCo\Framework;

abstract class AbstractForm extends \WebServCo\Framework\AbstractLibrary
{
    protected $errors = [];
    
    use \WebServCo\Framework\Traits\ExposeLibrariesTrait;
    
    public function __construct($settings)
    {
        parent::__construct($settings);
        
        foreach ($this->setting('meta', []) as $field => $title) {
            /**
             * Set form data from POST request.
             */
            $this->setData($field, $this->request()->data($field));
        }
    }
    
    abstract protected function db();
    abstract protected function validate();
    
    final public function asArray()
    {
        return [
            'meta' => $this->setting('meta', []),
            'data' => $this->data,
            'errors' => $this->errors,
        ];
    }
    
    final public function isSent()
    {
        return $this->request()->getMethod() === \WebServCo\Framework\Http::METHOD_POST;
    }
    
    final public function isValid()
    {
        return $this->validate();
    }
}
