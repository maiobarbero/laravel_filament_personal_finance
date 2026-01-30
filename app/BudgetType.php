<?php

namespace App\Enums;

enum BudgetType: string
{
    case RESET = 'reset';
    case ROLLOVER = 'rollover';
}
