<?php

namespace App\Filament\Widgets;

use App\Enums\BudgetType;
use App\Models\Budget;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class BudgetsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $startDate = $startDate ? Carbon::parse($startDate) : now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : now();

        $budgets = Budget::query()

            ->with(['transactions' => function ($query) use ($startDate, $endDate) {
                $query->whereDate('date', '>=', $startDate)
                    ->whereDate('date', '<=', $endDate);
            }])
            ->get();

        $stats = [];
        $diffInDays = $startDate->diffInDays($endDate);

        /** @var Budget $budget */
        foreach ($budgets as $budget) {
            $budgetAmount = $this->calculateBudgetAmount($budget, $startDate, $endDate);
            $spent = $this->calculateSpent($budget);
            $percentage = $budgetAmount > 0 ? ($spent / $budgetAmount) * 100 : 0;
            $remaining = $budgetAmount - $spent;

            $color = $this->getColorForPercentage($percentage);
            $icon = $this->getIconForPercentage($percentage);
            $chartData = $this->generateChartData($percentage);

            $stat = Stat::make(
                $budget->name,
                Number::percentage($percentage, 1)
            )
                ->description($this->formatBudgetDescription($budget, $spent, $budgetAmount, $remaining))
                ->descriptionIcon($icon)
                ->color($color)
                ->chart($chartData);

            $stats[] = $stat;
        }

        return $stats;
    }

    private function formatBudgetDescription(Budget $budget, float $spent, float $budgetAmount, float $remaining): string
    {
        $typeLabel = $budget->type === BudgetType::Rollover ? '🔄' : '🔁';
        $spentFormatted = Number::currency($spent, auth()->user()->currency, auth()->user()->locale);
        $budgetFormatted = Number::currency($budgetAmount, auth()->user()->currency, auth()->user()->locale);
        $remainingFormatted = Number::currency($remaining, auth()->user()->currency, auth()->user()->locale);

        return "{$typeLabel} Spent: {$spentFormatted} / {$budgetFormatted} • {$remainingFormatted} left";
    }

    private function getColorForPercentage(float $percentage): string
    {
        if ($percentage >= 100) {
            return 'danger';
        }

        if ($percentage >= 75) {
            return 'warning';
        }

        return 'success';
    }

    private function getIconForPercentage(float $percentage): string
    {
        if ($percentage >= 100) {
            return 'heroicon-m-exclamation-triangle';
        }

        if ($percentage >= 75) {
            return 'heroicon-m-arrow-trending-up';
        }

        return 'heroicon-m-check-circle';
    }

    /**
     * Generate chart data that visually represents the budget usage as a "progress bar" style chart.
     *
     * @return array<int, int>
     */
    private function generateChartData(float $percentage): array
    {
        $percentage = min($percentage, 100);

        $filledBars = (int) round($percentage / 10);
        $emptyBars = 10 - $filledBars;

        $data = [];
        for ($i = 0; $i < $filledBars; $i++) {
            $data[] = 100;
        }
        for ($i = 0; $i < $emptyBars; $i++) {
            $data[] = 10;
        }

        return $data;
    }

    private function calculateBudgetAmount(Budget $budget, Carbon $startDate, Carbon $endDate): float
    {
        $monthsDiff = $this->calculateMonthsDifference($startDate, $endDate);

        if ($budget->type === BudgetType::Reset) {
            $multiplier = max(1, $monthsDiff);

            return (float) $budget->amount * $multiplier;
        }

        // For Rollover, it is: (Monthly Amount * Months) + Previous Unspent
        $totalBudget = (float) $budget->amount * $monthsDiff;
        $previousUnspent = $this->calculatePreviousUnspent($budget, $startDate);

        return $totalBudget + $previousUnspent;
    }

    private function calculateMonthsDifference(Carbon $startDate, Carbon $endDate): int
    {

        $diff = $startDate->copy()->startOfMonth()->diffInMonths($endDate->copy()->startOfMonth());

        return (int) $diff + 1;
    }

    private function calculatePreviousUnspent(Budget $budget, Carbon $startDate): float
    {
        $createdAt = $budget->created_at;
        $startOfPeriod = $startDate->copy()->startOfMonth();

        if ($createdAt >= $startOfPeriod) {
            return 0;
        }

        $previousStartDate = $createdAt->copy()->startOfMonth();
        $previousEndDate = $startOfPeriod->copy()->subDay();

        $monthsBeforePeriod = $this->calculateMonthsDifference($previousStartDate, $previousEndDate);

        $totalPreviousBudget = (float) $budget->amount * $monthsBeforePeriod;

        $previousSpent = $budget->transactions()
            ->whereDate('date', '>=', $previousStartDate)
            ->whereDate('date', '<=', $previousEndDate)
            ->where('amount', '<', 0)
            ->sum('amount');

        $previousSpent = abs($previousSpent);

        return max(0, $totalPreviousBudget - $previousSpent);
    }

    private function calculateSpent(Budget $budget): float
    {
        return $budget->transactions
            ->filter(fn ($transaction) => $transaction->amount < 0)
            ->sum(fn ($transaction) => abs($transaction->amount));
    }
}
