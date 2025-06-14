<?php

namespace App\Models\Active;

use JsonSerializable;

class Active implements JsonSerializable
{
    public function __construct(
        protected int    $id,
        protected string $name,
        protected string $type
    )
    {
    }

    public function jsonSerialize(): mixed
    {
        return (object)get_object_vars($this);
    }
}
