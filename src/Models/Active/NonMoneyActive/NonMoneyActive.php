<?php

namespace App\Models\Active\NonMoneyActive;

use App\Models\Active\Active;

class NonMoneyActive extends Active
{

    public function __construct(
        int             $id,
        string          $name,
        string          $type,
        protected float $startBalanceCost,
        protected float $residualBalanceCost,
        protected float $finalBalanceCost
    )
    {
        parent::__construct($id, $name, $type);
    }
}
