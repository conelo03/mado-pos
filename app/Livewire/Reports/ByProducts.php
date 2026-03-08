<?php

namespace App\Livewire\Reports;

use App\Models\SaleItem;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ByProducts extends Component
{
    use WithPagination;

    public $startDate = '';
    public $endDate = '';
    public $productId = '';

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        $query = SaleItem::with('product', 'sale')
            ->whereHas('sale', function ($q) {
                $q->whereBetween('created_at', [
                    $this->startDate . ' 00:00:00',
                    $this->endDate . ' 23:59:59'
                ])->where('status', 'PAID');
            });

        if ($this->productId) {
            $query->where('product_id', $this->productId);
        }

        $items = $query->get();

        $report = $items->groupBy('product_id')->map(function ($group) {
            return [
                'product_name' => $group->first()->product->name,
                'qty' => $group->sum('qty'),
                'subtotal' => $group->sum('subtotal'),
            ];
        })->values();

        $totalQty = $report->sum('qty');
        $totalSubtotal = $report->sum('subtotal');

        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('livewire.reports.by-products', [
            'report' => $report,
            'totalQty' => $totalQty,
            'totalSubtotal' => $totalSubtotal,
            'products' => $products,
        ])->layout('components.app-layout', ['title' => 'Sales Report - By Products']);
    }
}
