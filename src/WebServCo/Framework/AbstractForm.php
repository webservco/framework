<?php
namespace WebServCo\Framework;

abstract class AbstractForm extends \WebServCo\Framework\AbstractLibrary
{
    protected $errors;

    protected $filtered;

    protected $valid;

    use \WebServCo\Framework\Traits\ExposeLibrariesTrait;

    public function __construct($settings, $defaultData = [])
    {
        parent::__construct($settings);

        /**
         * Set form data
         */
        foreach ($this->setting('meta', []) as $field => $title) {
            $this->setData(
                $field,
                $this->request()->data( // from POST
                    $field,
                    \WebServCo\Framework\Utils::arrayKey($field, $defaultData, null) // default data
                )
            );
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
        return $this->request()->getMethod() === \WebServCo\Framework\Http::METHOD_POST;
    }

    final public function isValid()
    {
        return $this->valid;
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
