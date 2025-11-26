<?php

declare(strict_types=1);

namespace WebServCo\Framework;

use WebServCo\Framework\Helpers\ArrayHelper;
use WebServCo\Framework\Http\Method;
use WebServCo\Framework\Traits\ExposeLibrariesTrait;

use function array_fill_keys;
use function in_array;
use function sprintf;

abstract class AbstractForm extends AbstractLibrary
{
    use ExposeLibrariesTrait;

    /**
     * Errors.
     *
     * @var array<string,array<int,string>>
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

    final public function clear(): bool
    {
        $this->clearData();
        $this->filtered = false;
        $this->errors = [];

        return true;
    }

    final public function errors(mixed $key, mixed $defaultValue = null): mixed
    {
        return ArrayStorage::get($this->errors, $key, $defaultValue);
    }

    /**
     * Get all errors.
     *
     * @return array<string,array<int,string>>
     */
    final public function getAllErrors(): array
    {
        return $this->errors;
    }

    final public function getSubmitField(): mixed
    {
        if (!$this->isSent() || !$this->submitFields) {
            return false;
        }

        return $this->submitField;
    }

    final public function help(mixed $key, mixed $defaultValue = null): mixed
    {
        return $this->setting(
            sprintf('help/%s', $key),
            $defaultValue,
        );
    }

    final public function isSent(): bool
    {
        if ($this->submitFields) {
            foreach ($this->submitFields as $field) {
                if ($this->request()->data($field) !== null) {
                    $this->submitField = $field;

                    return true;
                }
            }

            return false;
        }

        return $this->request()->getMethod() === Method::POST;
    }

    final public function isValid(): bool
    {
        return $this->valid;
    }

    final public function meta(mixed $key, mixed $defaultValue = null): mixed
    {
        return $this->setting(
            sprintf('meta/%s', $key),
            $defaultValue,
        );
    }

    final public function required(mixed $key): bool
    {
        // Retrieve list of required fields
        $required = $this->setting('required', []);

        // If exists, it's required.
        return in_array($key, $required, true);
    }

    /**
    * @return array<string, array<mixed>>
    */
    final public function toArray(): array
    {
        return [
            'custom' => $this->setting('custom', []),
            'data' => $this->getData(),
            'errors' => $this->errors,
            'help' => $this->setting('help', []),
            'meta' => $this->setting('meta', []),
            'required' => array_fill_keys($this->setting('required', []), true),
        ];
    }

    abstract public function validate(): bool;

    abstract protected function filter(): bool;

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
            $data = $this->isSent()
                ? $this->request()->data($field, null)
                : ArrayHelper::get($defaultData, $field, null);
            $this->setData($field, $data);
        }

        $this->errors = [];

        $this->filtered = $this->filter();

        $this->valid = false;

        if (!$this->isSent()) {
            return;
        }

        $this->valid = $this->validate();
    }
}
