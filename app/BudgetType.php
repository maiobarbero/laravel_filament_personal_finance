<?php

namespace App;

enum BudgetType: string
{
    case RESET = 'reset';
    case ROLLOVER = 'rollover';
}
