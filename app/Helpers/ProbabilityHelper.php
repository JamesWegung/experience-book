<?php

namespace App\Helpers;

class ProbabilityHelper
{
    public static function chance($percent): bool
    {
        return random_int(1, 100) <= $percent;
    }

}
