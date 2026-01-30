<?php

namespace App\Enums;

enum BudgetType: string
{
    case Reset = 'reset';
    case Rollover = 'rollover';

    public function getLabel(): string
    {
        return match ($this) {
            self::Reset => 'Reset',
            self::Rollover => 'Rollover',
        };
    }
}
