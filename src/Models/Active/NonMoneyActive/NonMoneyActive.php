<?php

namespace App\Models\Active\NonMoneyActive;

use App\Models\Active\Active;

class NonMoneyActive extends Active
{
    private float $startBalanceCost;
    private float $residualBalanceCost;
    private float $finalBalanceCost;
}