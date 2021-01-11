<?php
namespace WebServCo\Framework\Traits;

use WebServCo\Framework\Interfaces\ConstantValueClassInterface;

trait ConstantValueClassTrait
{
    /**
    * @var array<int, mixed>
    */
    private static array $instances = [];

    private int $value;

    protected static function constant(int $value) : self
    {
        return self::$instances[$value] ?? self::$instances[$value] = new static($value);
    }

    final private function __construct(int $value)
    {
        $this->value = $value;
    }
}
