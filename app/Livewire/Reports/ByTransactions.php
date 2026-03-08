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

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        $query = Sale::whereBetween('created_at', [
            $this->startDate . ' 00:00:00',
            $this->endDate . ' 23:59:59'
        ]);

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(10);

        $totalQuery = Sale::whereBetween('created_at', [
            $this->startDate . ' 00:00:00',
            $this->endDate . ' 23:59:59'
        ]);

        if ($this->status) {
            $totalQuery->where('status', $this->status);
        }

        $totalRevenue = $totalQuery->sum('total_price');
        $totalCost = $totalQuery->sum('total_cost');

        return view('livewire.reports.by-transactions', [
            'sales' => $sales,
            'totalRevenue' => $totalRevenue,
            'totalCost' => $totalCost,
        ])->layout('components.app-layout', ['title' => 'Sales Report - By Transactions']);
    }
}
