<?php

namespace App\Models\Active\MoneyActive;

use App\Models\Active\Active;

class MoneyActive extends Active
{


    public function __construct(
        int             $id,
        string          $name,
        string          $type,
        protected float $totalCost
    )
    {
        parent::__construct($id, $name, $type);
        $this->type = 'money';
    }

}
