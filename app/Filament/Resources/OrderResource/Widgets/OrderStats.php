<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Number;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('New Orders', Order::query()->where('status', 'new')->count())
                ->color('info')
                ->icon('heroicon-o-shopping-cart'),

            Stat::make('Processing Orders', Order::query()->where('status', 'processing')->count())
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Stat::make('Shipped Orders', Order::query()->where('status', 'shipped')->count())
                ->color('success')
                ->icon('heroicon-o-truck'),

            Stat::make('Average Price', '$' . number_format(Order::query()->avg('grand_total'), 2))
                ->color('primary')
                ->icon('heroicon-o-currency-dollar'),
        ];
    }
}
