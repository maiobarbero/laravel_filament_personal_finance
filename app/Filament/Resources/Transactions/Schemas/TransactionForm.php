<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('bank_account_id')
                    ->relationship('bankAccount', 'name', fn ($query) => $query->where('user_id', auth()->id()))
                    ->required(),
                TextInput::make('description')
                    ->required(),
                Select::make('category_id')
                    ->relationship('category', 'name', fn ($query) => $query->where('user_id', auth()->id()))
                    ->required(),
                Select::make('budget_id')
                    ->relationship('budget', 'name', fn ($query) => $query->where('user_id', auth()->id()))
                    ->required(),
                DatePicker::make('date')
                    ->required(),
                Textarea::make('note')
                    ->columnSpanFull(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
            ]);
    }
}
