<?php

namespace App\Models\Active\MoneyActive;

class NonBankMoneyActive extends MoneyActive
{
    public function __construct(
        int $id,
        string $name,
        float $totalCost,
        protected string $currency
    ) {
        parent::__construct($id, $name, $totalCost);
    }
}
