<?php

namespace App\Filament\Widgets;

use App\Models\BankAccount;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Collection;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $accounts = BankAccount::select('id', 'name', 'balance')->where('user_id', auth()->id())->get();
        $stats = [];

        $stats[] = Stat::make('Total Balance', "€ {$this->calculateTotalBalance($accounts)}");

        foreach ($accounts as $account) {
            $stats[] = Stat::make($account->name, "€ {$account->balance}");
        }

        return $stats;
    }

    protected function calculateTotalBalance(Collection $accounts): float
    {
        $totalBalance = 0;
        foreach ($accounts as $account) {
            $totalBalance += $account->balance;
        }

        return $totalBalance;
    }
}
