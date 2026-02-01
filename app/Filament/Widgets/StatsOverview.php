<?php

namespace App\Filament\Widgets;

use App\Models\BankAccount;
use App\Models\Transaction;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;

class StatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $accounts = BankAccount::select('id', 'name', 'balance')->get();
        $transactions = Transaction::query()
            ->when($startDate, fn ($query) => $query->whereDate('date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('date', '<=', $endDate))
            ->get();
        $stats = [];

        $stats[] = Stat::make('Total Balance', Number::currency($this->calculateTotalBalance($accounts), 'EUR', 'en'));

        foreach ($accounts as $account) {
            $stats[] = Stat::make($account->name, Number::currency($account->balance, 'EUR', 'en'));
        }

        $stats[] = Stat::make('Expenses', Number::currency($this->calculateExpenses($transactions), 'EUR', 'en'));
        $stats[] = Stat::make('Incomes', Number::currency($this->calculateIncomes($transactions), 'EUR', 'en'));
        $stats[] = Stat::make('Cash Flow', Number::currency($this->calculateCashFlow($transactions), 'EUR', 'en'));

        return $stats;
    }

    private function calculateTotalBalance(Collection $accounts): float
    {
        $totalBalance = 0;
        foreach ($accounts as $account) {
            $totalBalance += $account->balance;
        }

        return $totalBalance;
    }

    private function calculateExpenses(Collection $transactions): float
    {
        $expenses = 0;
        foreach ($transactions as $transaction) {
            if ($transaction->amount < 0) {
                $expenses += abs($transaction->amount);
            }
        }

        return $expenses;
    }

    private function calculateIncomes(Collection $transactions): float
    {
        $incomes = 0;
        foreach ($transactions as $transaction) {
            if ($transaction->amount > 0) {
                $incomes += $transaction->amount;
            }
        }

        return $incomes;
    }

    private function calculateCashFlow(Collection $transactions): float
    {
        return $this->calculateIncomes($transactions) - $this->calculateExpenses($transactions);
    }
}
