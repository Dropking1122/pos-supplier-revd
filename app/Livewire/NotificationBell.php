<?php
namespace App\Livewire;
use App\Models\{Product, Sale};
use Livewire\Component;

class NotificationBell extends Component
{
    public int $seenCount = 0;

    public function mount(): void
    {
        $this->seenCount = (int) session('bell_seen_count', 0);
    }

    public function markSeen(): void
    {
        $count = min(
            Product::whereColumn('kuantitas', '<=', 'stock_minimum')->count()
                + Sale::whereDate('created_at', today())->count(),
            99
        );
        $this->seenCount = $count;
        session(['bell_seen_count' => $count]);
    }

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

        $badgeCount = min($lowStockProducts->count() + $todaySalesCount, 99);

        return view('livewire.notification-bell', compact(
            'lowStockProducts', 'todaySales', 'todaySalesCount', 'badgeCount'
        ));
    }
}
