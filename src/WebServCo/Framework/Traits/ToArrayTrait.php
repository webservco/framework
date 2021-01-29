<?php
namespace WebServCo\Framework\Traits;

trait ToArrayTrait
{
    /**
    * This works only for public members.
    * @return array<string,mixed>
    */
    public function toArray(): array
    {
        return json_decode((string) json_encode($this), true);
    }
}
