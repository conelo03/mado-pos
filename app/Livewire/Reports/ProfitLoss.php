<?php

namespace App\Livewire\Reports;

use App\Models\Expense;
use App\Models\Sale;
use Livewire\Component;

class ProfitLoss extends Component
{
    public $startDate = '';
    public $endDate = '';

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        $inRange = fn ($query, $column) => $query
            ->when($this->startDate, fn ($q) => $q->where($column, '>=', $this->startDate . ' 00:00:00'))
            ->when($this->endDate, fn ($q) => $q->where($column, '<=', $this->endDate . ' 23:59:59'));

        $sold = $inRange(Sale::whereIn('status', ['PAID', 'REFUND']), 'created_at');
        $revenueCash = (clone $sold)->where('payment_method', 'CASH')->sum('total_price');
        $revenueNonCash = (clone $sold)->where('payment_method', 'NON_CASH')->sum('total_price');
        $transactionCount = (clone $sold)->count();
        $cogs = (clone $sold)->sum('total_cost');

        $refunds = $inRange(Sale::where('status', 'REFUND'), 'refunded_at');
        $refundCount = (clone $refunds)->count();
        $refundAmount = (clone $refunds)->sum('total_price');

        $revenue = $revenueCash + $revenueNonCash - $refundAmount;
        $grossProfit = $revenue - $cogs;

        $expensesByCategory = Expense::query()
            ->when($this->startDate, fn ($q) => $q->whereDate('date', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->whereDate('date', '<=', $this->endDate))
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();
        $totalExpenses = $expensesByCategory->sum('total');

        $netProfit = $grossProfit - $totalExpenses;

        return view('livewire.reports.profit-loss', [
            'revenueCash' => $revenueCash,
            'revenueNonCash' => $revenueNonCash,
            'revenue' => $revenue,
            'transactionCount' => $transactionCount,
            'cogs' => $cogs,
            'refundCount' => $refundCount,
            'refundAmount' => $refundAmount,
            'grossProfit' => $grossProfit,
            'expensesByCategory' => $expensesByCategory,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
        ])->layout('components.app-layout', ['title' => __('Profit & Loss')]);
    }
}
