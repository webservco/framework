<?php

namespace WebServCo\Framework\Traits;

trait ToArrayTrait
{
    /*
    * This works only for public methods.
    */
    public function toArray()
    {
        return json_decode((string) json_encode($this), true);
    }
}
