<?php

namespace App\Models\Active;

use JsonSerializable;

class Active implements JsonSerializable
{
    protected string $type = '';

    public function __construct(
        protected int $id,
        protected string $name
    )
    {}

//    public function getId(): int
//    {
//        return $this->id;
//    }
//
//    public function getName(): string
//    {
//        return $this->name;
//    }
//
//    public function getDetails(): string
//    {
//        $detailsString = implode(", ", $this->details);
//        return mb_ucfirst($detailsString);
//    }

    public function jsonSerialize(): mixed
    {
        return (object) get_object_vars($this);
    }
}
