<?php

namespace App\Livewire\Reports;

use App\Models\Sale;
use Livewire\Component;
use Livewire\WithPagination;

class ByTransactions extends Component
{
    use WithPagination;

    public $startDate = '';
    public $endDate = '';
    public $status = 'PAID';
    public $customerFilter = '';
    public $priceListTypeFilter = '';

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        $query = Sale::with('customer', 'priceListType')
            ->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ]);

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->customerFilter) {
            $query->where('customer_id', $this->customerFilter);
        }

        if ($this->priceListTypeFilter) {
            $query->where('price_list_type_id', $this->priceListTypeFilter);
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(10);

        $totalQuery = Sale::whereBetween('created_at', [
            $this->startDate . ' 00:00:00',
            $this->endDate . ' 23:59:59'
        ]);

        if ($this->status) {
            $totalQuery->where('status', $this->status);
        }

        if ($this->customerFilter) {
            $totalQuery->where('customer_id', $this->customerFilter);
        }

        if ($this->priceListTypeFilter) {
            $totalQuery->where('price_list_type_id', $this->priceListTypeFilter);
        }

        $totalRevenue = $totalQuery->sum('total_price');
        $totalCost = $totalQuery->sum('total_cost');

        $customers = \App\Models\Customer::all();
        $priceListTypes = \App\Models\PriceListType::all();

        return view('livewire.reports.by-transactions', [
            'sales' => $sales,
            'totalRevenue' => $totalRevenue,
            'totalCost' => $totalCost,
            'customers' => $customers,
            'priceListTypes' => $priceListTypes,
        ])->layout('components.app-layout', ['title' => 'Sales Report - By Transactions']);
    }
}
