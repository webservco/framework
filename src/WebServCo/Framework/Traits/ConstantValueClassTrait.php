<?php
namespace WebServCo\Framework\Traits;

trait ConstantValueClassTrait
{
    /**
    * @var array<int, mixed>
    */
    private static array $instances = [];

    /**
    * @var float|int|string
    */
    private $value;

    /**
    *  @param float|int|string $value
    */
    final private function __construct($value)
    {
        $this->value = $value;
    }

    /**
    * @param float|int|string $value
    * @return self
    */
    private static function constant($value) : self
    {
        return self::$instances[$value] ?? self::$instances[$value] = new self($value);
    }

    /**
    * @param float|int|string $value
    * @return self
    */
    public static function fromValue($value): self
    {
        $constants = self::getConstants();
        if (!in_array($value, $constants)) {
            throw new \InvalidArgumentException(sprintf('Invalid argument: "%s".', $value));
        }

        return self::constant($value);
    }

    /**
    * @return array<string,float,int|string>
    */
    private static function getConstants() : array
    {
        $reflection = new \ReflectionClass(__CLASS__);
        return $reflection->getConstants();
    }

    /**
    * Return class instance constant value.
    *
    * @return float|int|string
    */
    public function value()
    {
        return $this->value;
    }
}
