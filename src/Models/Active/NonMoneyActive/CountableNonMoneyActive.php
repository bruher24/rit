<?php

namespace App\Models\Active\NonMoneyActive;

class CountableNonMoneyActive extends NonMoneyActive
{
    public function __construct(
        int              $id,
        string           $name,
        string           $type,
        float            $startBalanceCost,
        float            $residualBalanceCost,
        float            $finalBalanceCost,
        protected string $inventoryNumber,
        protected string $measureUnits,
        protected string $productionDate
    )
    {
        parent::__construct(
            $id,
            $name,
            $type,
            $startBalanceCost,
            $residualBalanceCost,
            $finalBalanceCost
        );
    }
}