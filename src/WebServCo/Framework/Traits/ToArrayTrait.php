<?php

declare(strict_types=1);

namespace WebServCo\Framework\Traits;

use function json_decode;
use function json_encode;

trait ToArrayTrait
{
    /**
    * This works only for public members.
     *
     * @return array<string,mixed>
    */
    public function toArray(): array
    {
        return json_decode((string) json_encode($this), true);
    }
}
