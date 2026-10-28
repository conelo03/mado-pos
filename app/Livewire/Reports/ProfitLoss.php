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
        $range = [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'];

        $paid = Sale::where('status', 'PAID')->whereBetween('created_at', $range);
        $revenueCash = (clone $paid)->where('payment_method', 'CASH')->sum('total_price');
        $revenueNonCash = (clone $paid)->where('payment_method', 'NON_CASH')->sum('total_price');
        $revenue = $revenueCash + $revenueNonCash;
        $transactionCount = (clone $paid)->count();
        $cogs = (clone $paid)->sum('total_cost');

        $refunds = Sale::where('status', 'REFUND')->whereBetween('created_at', $range);
        $refundCount = (clone $refunds)->count();
        $refundAmount = (clone $refunds)->sum('total_price');
        $refundCost = (clone $refunds)->sum('total_cost');

        $grossProfit = $revenue - $cogs - $refundCost;

        $expensesByCategory = Expense::whereDate('date', '>=', $this->startDate)
            ->whereDate('date', '<=', $this->endDate)
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
            'refundCost' => $refundCost,
            'grossProfit' => $grossProfit,
            'expensesByCategory' => $expensesByCategory,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
        ])->layout('components.app-layout', ['title' => __('Profit & Loss')]);
    }
}
