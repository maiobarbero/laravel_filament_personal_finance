<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('description')
                    ->required(),
                Select::make('bank_account_id')
                    ->relationship('bankAccount', 'name', fn ($query) => $query->where('user_id', auth()->id()))
                    ->required(),
                Select::make('category_id')
                    ->relationship('category', 'name', fn ($query) => $query->where('user_id', auth()->id()))
                    ->required(),
                Select::make('budget_id')
                    ->relationship('budget', 'name', fn ($query) => $query->where('user_id', auth()->id()))
                    ->required(),
                Textarea::make('note')
                    ->columnSpanFull(),
            ]);
    }
}
