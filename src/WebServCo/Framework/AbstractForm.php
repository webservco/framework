<?php

declare(strict_types=1);

namespace WebServCo\Framework;

abstract class AbstractForm extends \WebServCo\Framework\AbstractLibrary
{
    use \WebServCo\Framework\Traits\ExposeLibrariesTrait;

    /**
     * Errors.
     *
     * @var array<string, array<int,string>>
     */
    protected array $errors;

    protected bool $filtered;

    /**
     * Submit fields.
     *
     * @var array<int,string>
     */
    protected array $submitFields;

    protected string $submitField;

    protected bool $valid;

    abstract protected function filter(): bool;

    abstract protected function validate(): bool;

    /**
    * @param array<string,string|array<mixed>> $settings
    * @param array<string,bool|int|float|string|null> $defaultData
    * @param array<int,string> $submitFields
    */
    public function __construct(array $settings, array $defaultData = [], array $submitFields = [])
    {
        parent::__construct($settings);

        $this->submitFields = $submitFields;

        /**
         * Set form data
         */
        foreach ($this->setting('meta', []) as $field => $title) {
            $data = $this->isSent() ? $this->request()->data($field, null) : \WebServCo\Framework\Utils\Arrays::get(
                $defaultData,
                $field,
                null,
            );
            $this->setData($field, $data);
        }

        $this->errors = [];

        $this->filtered = $this->filter();

        if (!$this->isSent()) {
            return;
        }

        $this->valid = $this->validate();
    }

    final public function clear(): bool
    {
        $this->clearData();
        $this->filtered = false;
        $this->errors = [];
        return true;
    }

    /**
     * @param mixed $key
     * @param mixed $defaultValue
     * @return mixed
     */
    final public function errors($key, $defaultValue = false)
    {
        return \WebServCo\Framework\ArrayStorage::get($this->errors, $key, $defaultValue);
    }

    /**
    * @return mixed
    */
    final public function getSubmitField()
    {
        if (!$this->isSent() || empty($this->submitFields)) {
            return false;
        }
        return $this->submitField;
    }

    /**
     * @param mixed $key
     * @param mixed $defaultValue
     * @return mixed
     */
    final public function help($key, $defaultValue = false)
    {
        return $this->setting(
            \sprintf('help/%s', $key),
            $defaultValue,
        );
    }

    final public function isSent(): bool
    {
        if (!empty($this->submitFields)) {
            foreach ($this->submitFields as $field) {
                if (false !== $this->request()->data($field)) {
                    $this->submitField = $field;
                    return true;
                }
            }
            return false;
        }
        return \WebServCo\Framework\Http\Method::POST === $this->request()->getMethod();
    }

    final public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @param mixed $key
     * @param mixed $defaultValue
     * @return mixed
     */
    final public function meta($key, $defaultValue = false)
    {
        return $this->setting(
            \sprintf('meta/%s', $key),
            $defaultValue,
        );
    }

    /**
     * @param mixed $key
     * @param mixed $defaultValue
     * @return mixed
     */
    final public function required($key, $defaultValue = false)
    {
        return $this->setting(
            \sprintf('required/%s', $key),
            $defaultValue,
        );
    }

    /**
    * @return array<string, array<mixed>>
    */
    final public function toArray(): array
    {
        return [
            'meta' => $this->setting('meta', []),
            'help' => $this->setting('help', []),
            'required' => \array_fill_keys($this->setting('required', []), true),
            'custom' => $this->setting('custom', []),
            'data' => $this->getData(),
            'errors' => $this->errors,
        ];
    }
}
