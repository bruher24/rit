<?php

namespace App\Models\Active\MoneyActive;

class BankMoneyActive extends MoneyActive
{
    public function __construct(
        int $id,
        string $name,
        float $totalCost,
        protected string $bankName,
        protected string $accountNumber
    ) {
        parent::__construct($id, $name, $totalCost);
    }

//    public function getBankName(): string
//    {
//        return $this->bankName;
//    }
//
//    public function getAccountNumber(): string
//    {
//        return $this->accountNumber;
//    }
}
