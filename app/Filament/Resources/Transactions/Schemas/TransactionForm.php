<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->required(),
                Group::make([
                    ToggleButtons::make('transaction_type')
                        ->label('Type')
                        ->options([
                            'expense' => 'Expense',
                            'income' => 'Income',
                        ])
                        ->icons([
                            'expense' => 'heroicon-o-minus-circle',
                            'income' => 'heroicon-o-plus-circle',
                        ])
                        ->colors([
                            'expense' => 'danger',
                            'income' => 'success',
                        ])
                        ->default('expense')
                        ->inline()
                        ->required()
                        ->afterStateHydrated(function (ToggleButtons $component, $state, $record) {
                            if ($record && $record->amount !== null) {
                                $component->state($record->amount >= 0 ? 'income' : 'expense');
                            }
                        })
                        ->live(),
                    TextInput::make('amount')
                        ->label('Amount')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->prefix(fn (Get $get) => $get('transaction_type') === 'income' ? '+' : '-')
                        ->afterStateHydrated(function (TextInput $component, $state) {
                            if ($state !== null) {
                                $component->state(abs($state));
                            }
                        })
                        ->dehydrateStateUsing(function ($state, Get $get) {
                            $amount = abs((float) $state);

                            return $get('transaction_type') === 'income' ? $amount : -$amount;
                        }),
                ])->columns(2),
                TextInput::make('description')
                    ->required(),
                Select::make('bank_account_id')
                    ->relationship('bankAccount', 'name', fn ($query) => $query->where('user_id', auth()->id()))
                    ->required(),
                Select::make('category_id')
                    ->relationship('category', 'name', fn ($query) => $query->where('user_id', auth()->id())),
                Select::make('budget_id')
                    ->relationship('budget', 'name', fn ($query) => $query->where('user_id', auth()->id())),
                Textarea::make('note')
                    ->columnSpanFull(),
            ]);
    }
}
