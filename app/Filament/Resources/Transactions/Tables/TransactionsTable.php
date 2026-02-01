<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('amount')
                    ->money(fn () => auth()->user()->currency, locale: fn () => auth()->user()->locale)
                    ->sortable()
                    ->badge()
                    ->color(fn ($state): string => $state >= 0 ? 'success' : 'danger')
                    ->icon(fn ($state): string => $state >= 0 ? 'heroicon-o-arrow-up' : 'heroicon-o-arrow-down'),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('bankAccount.name')
                    ->searchable(true),
                TextColumn::make('category.name')
                    ->searchable(true),
                TextColumn::make('budget.name')
                    ->searchable(true),
                TextColumn::make('note')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('transaction_type')
                    ->label('Tipo')
                    ->options([
                        'income' => 'Income',
                        'expense' => 'Expense',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['value'] === 'income',
                                fn (Builder $query): Builder => $query->where('amount', '>=', 0),
                            )
                            ->when(
                                $data['value'] === 'expense',
                                fn (Builder $query): Builder => $query->where('amount', '<', 0),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
