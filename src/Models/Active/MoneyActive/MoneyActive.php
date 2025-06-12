<?php

namespace App\Models\Active\MoneyActive;

use App\Models\Active\Active;

class MoneyActive extends Active
{


    public function __construct(
        int $id,
        string $name,
        protected float $totalCost
    ) {
        parent::__construct($id, $name);
        $this->type = 'money';
    }

//    public function getTotalCost(): float
//    {
//        return $this->totalCost;
//    }
}
