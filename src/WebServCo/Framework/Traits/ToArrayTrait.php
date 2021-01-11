<?php
namespace WebServCo\Framework\Traits;

trait ToArrayTrait
{
    /**
    * This works only for public methods.
    * @return array<mixed>
    */
    public function toArray() : array
    {
        return json_decode((string) json_encode($this), true);
    }
}
