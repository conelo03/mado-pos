<?php

namespace App\Livewire\Reports;

use App\Livewire\Concerns\ExportsCsv;
use App\Models\Sale;
use Livewire\Component;
use Livewire\WithPagination;

class ByTransactions extends Component
{
    use ExportsCsv;
    use WithPagination;

    public $startDate = '';
    public $endDate = '';
    public $status = 'PAID';
    public $customerFilter = '';
    public $priceListTypeFilter = '';
    public $paymentMethodFilter = '';

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    private function filteredQuery()
    {
        return Sale::query()
            ->when($this->startDate, fn ($q) => $q->where('created_at', '>=', $this->startDate . ' 00:00:00'))
            ->when($this->endDate, fn ($q) => $q->where('created_at', '<=', $this->endDate . ' 23:59:59'))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->customerFilter, fn ($q) => $q->where('customer_id', $this->customerFilter))
            ->when($this->priceListTypeFilter, fn ($q) => $q->where('price_list_type_id', $this->priceListTypeFilter))
            ->when($this->paymentMethodFilter, fn ($q) => $q->where('payment_method', $this->paymentMethodFilter));
    }

    public function export()
    {
        $sales = $this->filteredQuery()
            ->with('customer', 'priceListType')
            ->orderBy('created_at', 'desc')
            ->get();

        $rows = $sales->map(fn ($sale) => [
            $sale->invoice_no,
            $sale->created_at->format('d/m/Y H:i'),
            $sale->customer?->name ?? __('Walk-in Customer'),
            $sale->priceListType?->name ?? '-',
            Sale::paymentMethodLabel($sale->payment_method),
            __($sale->status),
            (float) $sale->total_cost,
            (float) $sale->total_price,
        ]);

        return $this->csvDownload('laporan-transaksi_' . $this->csvPeriod() . '.csv', [
            __('Invoice'),
            __('Date'),
            __('Customer'),
            __('Price Type'),
            __('Payment'),
            __('Status'),
            __('Total Cost'),
            __('Total Revenue'),
        ], $rows);
    }

    public function render()
    {
        $query = $this->filteredQuery();

        $totalRevenue = (clone $query)->sum('total_price');
        $totalCost = (clone $query)->sum('total_cost');

        $sales = $query->with('customer', 'priceListType')->orderBy('created_at', 'desc')->paginate(10);

        $customers = \App\Models\Customer::all();
        $priceListTypes = \App\Models\PriceListType::all();

        return view('livewire.reports.by-transactions', [
            'sales' => $sales,
            'totalRevenue' => $totalRevenue,
            'totalCost' => $totalCost,
            'customers' => $customers,
            'priceListTypes' => $priceListTypes,
        ])->layout('components.app-layout', ['title' => __('Sales Report - By Transactions')]);
    }
}
