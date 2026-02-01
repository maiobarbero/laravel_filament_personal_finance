<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->components([
                        Actions::make([
                            Action::make('Last Week')
                                ->action(function (Set $set) {
                                    $set('startDate', now()->subWeek());
                                    $set('endDate', now());
                                }),
                            Action::make('Last Month')
                                ->action(function (Set $set) {
                                    $set('startDate', now()->subMonth());
                                    $set('endDate', now());
                                }),
                            Action::make('Last Year')
                                ->action(function (Set $set) {
                                    $set('startDate', now()->subYear());
                                    $set('endDate', now());
                                }),
                            Action::make('Reset')
                                ->color('danger')
                                ->icon('heroicon-m-x-mark')
                                ->action(function (Set $set) {
                                    $set('startDate', null);
                                    $set('endDate', null);
                                }),
                        ]),
                    ])
                    ->columnSpanFull()
                    ->columns(1),
                Section::make()
                    ->components([
                        DatePicker::make('startDate')
                            ->live()
                            ->maxDate(fn (Get $get) => $get('endDate') ?? now())
                            ->default(now()->subWeek()),
                        DatePicker::make('endDate')
                            ->live()
                            ->minDate(fn (Get $get) => $get('startDate') ?? null)
                            ->maxDate(now())
                            ->default(now()),
                    ])
                    ->columnSpanFull()
                    ->columns(2),
            ]);
    }
}
