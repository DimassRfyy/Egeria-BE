<?php

namespace App\Filament\Widgets;

use App\Models\TransactionDetail;
use Filament\Widgets\ChartWidget;

class PopularProductsChart extends ChartWidget
{
    protected static ?string $heading = 'Popular Products';

    public function getDescription(): ?string
{
    return 'This chart shows the most popular products.';
}

    protected static ?string $maxHeight = '32vh';

    protected function getData(): array
    {
        $topCosmetics = TransactionDetail::getTopCosmetics();

        $labels = $topCosmetics->pluck('cosmetic.name')->toArray();
        $data = $topCosmetics->pluck('total_quantity')->toArray();

        $baseColor = '#FF4D9E';
        $colors = array_map(function ($index) use ($baseColor, $data) {
            $opacity = 0.1 + (0.9 * ($index + 1) / count($data));
            return $this->hexToRgba($baseColor, $opacity);
        }, array_keys($data));

        return [
            'datasets' => [
                [
                    'label' => 'Most Purchased Cosmetics',
                    'data' => $data,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function hexToRgba($hex, $opacity)
    {
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 6) {
            list($r, $g, $b) = array_map('hexdec', str_split($hex, 2));
        } else {
            list($r, $g, $b) = array_map('hexdec', str_split($hex, 1));
            $r = $r * 17;
            $g = $g * 17;
            $b = $b * 17;
        }
        return "rgba($r, $g, $b, $opacity)";
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
