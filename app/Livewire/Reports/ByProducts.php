<?php

namespace App\Livewire\Reports;

use App\Models\SaleItem;
use App\Models\Item;
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
        $query = SaleItem::with('item', 'sale')
            ->whereHas('sale', function ($q) {
                $q->whereBetween('created_at', [
                    $this->startDate . ' 00:00:00',
                    $this->endDate . ' 23:59:59'
                ])->where('status', 'PAID');
            });

        if ($this->productId) {
            $query->where('item_id', $this->productId);
        }

        $items = $query->get();

        $report = $items->groupBy('item_id')->map(function ($group) {
            return [
                'product_name' => $group->first()->item->name,
                'qty' => $group->sum('qty'),
                'subtotal' => $group->sum('subtotal'),
                'cost_subtotal' => $group->sum('cost_subtotal'),
            ];
        })->values();

        $totalQty = $report->sum('qty');
        $totalSubtotal = $report->sum('subtotal');
        $totalCostSubtotal = $report->sum('cost_subtotal');

        $products = Item::where('type', 'PRODUCT')->where('is_active', true)->orderBy('name')->get();

        return view('livewire.reports.by-products', [
            'report' => $report,
            'totalQty' => $totalQty,
            'totalSubtotal' => $totalSubtotal,
            'totalCostSubtotal' => $totalCostSubtotal,
            'products' => $products,
        ])->layout('components.app-layout', ['title' => 'Sales Report - By Products']);
    }
}
