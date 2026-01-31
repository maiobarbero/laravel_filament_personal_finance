<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Charts extends PagE
{
    protected string $view = 'filament.pages.charts';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartPie;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\ExpensesPieChart::class,
        ];
    }
}
