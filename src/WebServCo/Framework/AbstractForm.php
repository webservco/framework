<?php
namespace WebServCo\Framework;

abstract class AbstractForm extends \WebServCo\Framework\AbstractLibrary
{
    protected $errors;

    protected $filtered;

    protected $submitFields;

    protected $submitField;

    protected $valid;

    use \WebServCo\Framework\Traits\ExposeLibrariesTrait;

    public function __construct($settings, $defaultData = [], $submitFields = [])
    {
        parent::__construct($settings);

        $this->submitFields = $submitFields;

        /**
         * Set form data
         */
        foreach ($this->setting('meta', []) as $field => $title) {
            if ($this->isSent()) {
                $data = $this->request()->data($field);
            } else {
                $data = \WebServCo\Framework\Utils\Arrays::get($defaultData, $field, null);
            }
            $this->setData($field, $data);
        }

        $this->errors = [];

        $this->filtered = $this->filter();

        if ($this->isSent()) {
            $this->valid = $this->validate();
        }
    }

    abstract protected function db();

    /**
     * @return bool
     */
    abstract protected function filter();

    /**
     * @return bool
     */
    abstract protected function validate();

    final public function isSent()
    {
        if (!empty($this->submitFields)) {
            foreach ($this->submitFields as $field) {
                if ($this->request()->data($field)) {
                    $this->submitField = $field;
                    return true;
                }
            }
            return false;
        }
        return $this->request()->getMethod() === \WebServCo\Framework\Http::METHOD_POST;
    }

    final public function getSubmitField()
    {
        if (!$this->isSent() || empty($this->submitFields)) {
            return false;
        }
        return $this->submitField;
    }

    final public function isValid()
    {
        return $this->valid;
    }

    final public function clear()
    {
        $this->data = [];
        $this->filtered = [];
        $this->errors = [];
    }

    final public function toArray()
    {
        return [
            'meta' => $this->setting('meta', []),
            'help' => $this->setting('help', []),
            'required' => array_fill_keys($this->setting('required', []), true),
            'data' => $this->data,
            'errors' => $this->errors,
        ];
    }
}
