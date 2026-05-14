<?php
namespace App\Livewire;
use App\Models\{Product, Sale};
use Livewire\Component;

class NotificationBell extends Component
{
    public function render()
    {
        $lowStockProducts = Product::whereColumn('kuantitas', '<=', 'stock_minimum')
            ->orderBy('kuantitas')
            ->get();

        $todaySales = Sale::with('customer')
            ->whereDate('created_at', today())
            ->latest()
            ->take(5)
            ->get();

        $todaySalesCount = Sale::whereDate('created_at', today())->count();

        $badgeCount = $lowStockProducts->count() + $todaySalesCount;
        $badgeCount = min($badgeCount, 99);

        return view('livewire.notification-bell', compact(
            'lowStockProducts', 'todaySales', 'todaySalesCount', 'badgeCount'
        ));
    }
}
