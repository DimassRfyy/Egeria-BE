<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\BookingTransaction;
use Carbon\Carbon;

class TransactionChart extends ChartWidget
{
    protected static ?string $heading = 'Transaction Chart';

    public function getDescription(): ?string
{
    return 'This chart shows the total transactions per month';
}

    protected static ?string $maxHeight = '33vh';

    public function getData(): array
    {
        $transactions = BookingTransaction::where('is_paid', true)->get();

        $monthlyTransactions = $transactions->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('M'); // Mengelompokkan berdasarkan bulan
        });

        $data = [];
        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        foreach ($labels as $month) {
            $totalAmount = $monthlyTransactions->has($month) ? $monthlyTransactions[$month]->sum('total_amount') : 0;
            $data[] = $totalAmount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Transaksi',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
