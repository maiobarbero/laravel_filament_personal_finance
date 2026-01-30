<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\BankAccount;

class BalanceOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $accounts = BankAccount::select("id", "name", "balance")->where("user_id", auth()->id())->get();
        $stats = [];
        foreach ($accounts as $account) {
            $stats[] = Stat::make($account->name, $account->balance);
        }
        return $stats;
    }
}
