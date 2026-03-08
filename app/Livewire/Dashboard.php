<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\Sale;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $totalProducts = Item::where('type', 'PRODUCT')->count();
        $totalRawMaterials = Item::where('type', 'RAW_MATERIAL')->count();
        $todaySales = Sale::whereDate('created_at', today())->where('status', 'PAID')->count();
        $todayRevenue = Sale::whereDate('created_at', today())->where('status', 'PAID')->sum('total_price');
        $recentTransactions = Sale::latest()->limit(10)->get();

        return view('livewire.dashboard', [
            'totalProducts' => $totalProducts,
            'totalRawMaterials' => $totalRawMaterials,
            'todaySales' => $todaySales,
            'todayRevenue' => $todayRevenue,
            'recentTransactions' => $recentTransactions,
        ])->layout('components.app-layout', ['title' => 'Dashboard']);
    }
}
