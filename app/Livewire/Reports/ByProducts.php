<?php

namespace App\Livewire\Reports;

use App\Livewire\Concerns\ExportsCsv;
use App\Models\SaleItem;
use App\Models\Item;
use Livewire\Component;
use Livewire\WithPagination;

class ByProducts extends Component
{
    use ExportsCsv;
    use WithPagination;

    public $startDate = '';
    public $endDate = '';
    public $productId = '';

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    private function reportRows()
    {
        return SaleItem::query()
            ->whereHas('sale', fn ($q) => $q
                ->where('status', 'PAID')
                ->when($this->startDate, fn ($q) => $q->where('created_at', '>=', $this->startDate . ' 00:00:00'))
                ->when($this->endDate, fn ($q) => $q->where('created_at', '<=', $this->endDate . ' 23:59:59')))
            ->when($this->productId, fn ($q) => $q->where('item_id', $this->productId))
            ->selectRaw('item_id, SUM(qty) as qty, SUM(subtotal) as subtotal, SUM(cost_subtotal) as cost_subtotal')
            ->groupBy('item_id')
            ->orderByRaw('MIN(id)')
            ->with('item')
            ->get()
            ->map(fn ($row) => [
                'product_name' => $row->item->name,
                'qty' => (float) $row->qty,
                'subtotal' => (float) $row->subtotal,
                'cost_subtotal' => (float) $row->cost_subtotal,
            ]);
    }

    public function export()
    {
        $rows = $this->reportRows()->map(fn ($row) => [
            $row['product_name'],
            $row['qty'],
            $row['cost_subtotal'],
            $row['subtotal'],
        ]);

        return $this->csvDownload('laporan-produk_' . $this->csvPeriod() . '.csv', [
            __('Product Name'),
            __('Qty'),
            __('Cost Subtotal'),
            __('Subtotal'),
        ], $rows);
    }

    public function render()
    {
        $report = $this->reportRows();

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
        ])->layout('components.app-layout', ['title' => __('Sales Report - By Products')]);
    }
}
