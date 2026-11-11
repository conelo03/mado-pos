<?php

namespace App\Livewire\Reports;

use App\Livewire\Concerns\ExportsCsv;
use App\Models\Expense;
use App\Models\Sale;
use Livewire\Component;

class ProfitLoss extends Component
{
    use ExportsCsv;

    public $startDate = '';
    public $endDate = '';

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    private function summary(): array
    {
        $inRange = fn ($query, $column) => $query
            ->when($this->startDate, fn ($q) => $q->where($column, '>=', $this->startDate . ' 00:00:00'))
            ->when($this->endDate, fn ($q) => $q->where($column, '<=', $this->endDate . ' 23:59:59'));

        $sold = $inRange(Sale::whereIn('status', ['PAID', 'REFUND']), 'created_at')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_price) as price, SUM(total_cost) as cost')
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');
        $revenueCash = (float) ($sold['CASH']->price ?? 0);
        $revenueNonCash = (float) ($sold['NON_CASH']->price ?? 0);
        $transactionCount = (int) $sold->sum('count');
        $cogs = (float) $sold->sum('cost');

        $refunds = $inRange(Sale::where('status', 'REFUND'), 'refunded_at')
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total_price), 0) as price')
            ->first();
        $refundCount = (int) $refunds->count;
        $refundAmount = (float) $refunds->price;

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

        return compact(
            'revenueCash',
            'revenueNonCash',
            'revenue',
            'transactionCount',
            'cogs',
            'refundCount',
            'refundAmount',
            'grossProfit',
            'expensesByCategory',
            'totalExpenses',
            'netProfit',
        );
    }

    public function export()
    {
        $data = $this->summary();

        $rows = [
            [__('Revenue'), ''],
            [__('Cash Sales'), $data['revenueCash']],
            [__('Non-cash Sales'), $data['revenueNonCash']],
            [__('Refund'), 0 - $data['refundAmount']],
            [__('Total Revenue'), $data['revenue']],
            [__('Cost of Goods'), ''],
            [__('Cost of goods sold'), 0 - $data['cogs']],
            [__('Gross Profit'), $data['grossProfit']],
            [__('Expenses'), ''],
            ...$data['expensesByCategory']->map(fn ($expense) => [$expense->category, 0 - (float) $expense->total]),
            [__('Total Expenses'), 0 - $data['totalExpenses']],
            [__('Net Profit'), $data['netProfit']],
        ];

        return $this->csvDownload('laba-rugi_' . $this->csvPeriod() . '.csv', [__('Profit & Loss'), ''], $rows);
    }

    public function render()
    {
        return view('livewire.reports.profit-loss', $this->summary())
            ->layout('components.app-layout', ['title' => __('Profit & Loss')]);
    }
}
