<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Models\Transaction;
use Filament\Forms\Components\Select;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class TransactionOverview extends ChartWidget
{
    protected static ?string $heading = 'Pengeluaran dan Pemasukan Tahun ini';

    // Add chart type state
    protected string $chartType = 'line';

    public function getHeaderWidgetsColumns(): int | array
{
    return 12;
}

    // Add header actions for the chart type selector
    protected function getHeaderActions(): array
    {
        return [
            Select::make('chartType')
                ->label('Tipe Grafik')
                ->options([
                    'line' => 'Line Chart',
                    'bar' => 'Bar Chart',
                    'radar' => 'Radar Chart',
                    'pie' => 'Pie Chart',
                    'doughnut' => 'Doughnut Chart',
                ])
                ->default('line')
                ->live()
                ->afterStateUpdated(function ($state) {
                    $this->chartType = $state;
                })
        ];
    }

    protected function getData(): array
    {
        $currentYear = now()->year;

        // Get all months of current year
        $months = collect(range(1, 12))->map(function ($month) use ($currentYear) {
            return now()->setYear($currentYear)->setMonth($month)->startOfMonth();
        });

        // Get monthly transactions
        $monthlyIncome = $months->map(function ($date) {
            return Transaction::where('type', 'masuk')
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');
        })->values()->toArray();

        $monthlyExpense = $months->map(function ($date) {
            return Transaction::where('type', 'keluar')
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');
        })->values()->toArray();

        // Get month labels
        $labels = $months->map(fn ($date) => $date->format('F'))->toArray();

        // Adjust data structure based on chart type
        if (in_array($this->chartType, ['pie', 'doughnut'])) {
            return [
                'labels' => ['Dana Masuk', 'Dana Keluar'],
                'datasets' => [
                    [
                        'data' => [
                            array_sum($monthlyIncome),
                            array_sum($monthlyExpense),
                        ],
                        'backgroundColor' => [
                            'rgba(34, 197, 94, 0.2)',
                            'rgba(239, 68, 68, 0.2)',
                        ],
                        'borderColor' => [
                            'rgb(34, 197, 94)',
                            'rgb(239, 68, 68)',
                        ],
                        'borderWidth' => 2,
                    ],
                ],
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Dana Masuk',
                    'data' => $monthlyIncome,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'fill' => $this->chartType === 'radar' ? true : false,
                ],
                [
                    'label' => 'Dana Keluar',
                    'data' => $monthlyExpense,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                    'fill' => $this->chartType === 'radar' ? true : false,
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        $baseOptions = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
            ],
        ];

        // Add specific options based on chart type
        if (!in_array($this->chartType, ['pie', 'doughnut'])) {
            $baseOptions['scales'] = [
                'y' => [
                    'beginAtZero' => true,
                ],
            ];
        }

        return $baseOptions;
    }

    protected function getType(): string
    {
        return $this->chartType;
    }

    public static function canView(): bool
    {
        return true;
    }
}
