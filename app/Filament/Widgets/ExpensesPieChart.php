<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class ExpensesPieChart extends ChartWidget
{
    use InteractsWithPageFilters;

    public function getHeading(): ?string
    {
        return 'Expenses by Category';
    }

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $data = Transaction::query()
            ->with('category')
            ->where('user_id', auth()->id())
            ->where('amount', '<', 0)
            ->whereNotNull('category_id')
            ->when($startDate, fn ($query) => $query->whereDate('date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('date', '<=', $endDate))
            ->get()
            ->groupBy('category.name')
            ->map(fn ($transactions) => abs($transactions->sum('amount')));

        return [
            'datasets' => [
                [
                    'label' => 'Expenses',
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => [
                        '#ef4444', '#f97316', '#f59e0b', '#84cc16', '#10b981',
                        '#06b6d4', '#3b82f6', '#6366f1', '#8b5cf6', '#d946ef',
                        '#f43f5e', '#64748b',
                    ],
                ],
            ],
            'labels' => $data->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
