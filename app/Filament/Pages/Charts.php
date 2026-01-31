<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class Charts extends Page
{
    protected string $view = 'filament.pages.charts';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::ChartPie;
}
