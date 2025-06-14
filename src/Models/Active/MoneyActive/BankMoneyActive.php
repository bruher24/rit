<?php

namespace App\Models\Active\MoneyActive;

class BankMoneyActive extends MoneyActive
{
    public function __construct(
        int              $id,
        string           $name,
        string           $type,
        float            $totalCost,
        protected string $bankName,
        protected string $accountNumber
    )
    {
        parent::__construct($id, $name, $type, $totalCost);
    }

}
