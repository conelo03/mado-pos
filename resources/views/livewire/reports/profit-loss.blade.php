<div>
    @php
        $rp = fn ($value) => ($value < 0 ? '-' : '') . 'Rp ' . number_format(abs($value), 0, ',', '.');
    @endphp

    <div class="mb-6 flex flex-col lg:flex-row gap-4 items-end">
        <div class="flex-1">
            <label class="label">
                <span class="label-text">{{ __('From Date') }}</span>
            </label>
            <input
                type="date"
                wire:model.live="startDate"
                class="input input-bordered w-full"
            >
        </div>
        <div class="flex-1">
            <label class="label">
                <span class="label-text">{{ __('To Date') }}</span>
            </label>
            <input
                type="date"
                wire:model.live="endDate"
                class="input input-bordered w-full"
            >
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-sm">{{ __('Revenue') }}</h2>
                <p class="text-2xl font-bold">{{ $rp($revenue) }}</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-sm">{{ __('Gross Profit') }}</h2>
                <p class="text-2xl font-bold">{{ $rp($grossProfit) }}</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-sm">{{ __('Expenses') }}</h2>
                <p class="text-2xl font-bold">{{ $rp($totalExpenses) }}</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-sm">{{ __('Net Profit') }}</h2>
                <p class="text-2xl font-bold {{ $netProfit < 0 ? 'text-error' : 'text-success' }}">{{ $rp($netProfit) }}</p>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table w-full">
            <tbody>
                <tr class="bg-base-200">
                    <th colspan="2">{{ __('Revenue') }}</th>
                </tr>
                <tr>
                    <td class="pl-8">{{ __('Cash Sales') }}</td>
                    <td class="text-right">{{ $rp($revenueCash) }}</td>
                </tr>
                <tr>
                    <td class="pl-8">{{ __('Non-cash Sales') }}</td>
                    <td class="text-right">{{ $rp($revenueNonCash) }}</td>
                </tr>
                <tr class="font-semibold">
                    <td>{{ __('Total Revenue') }} <span class="font-normal opacity-60">({{ __(':count transactions', ['count' => $transactionCount]) }})</span></td>
                    <td class="text-right">{{ $rp($revenue) }}</td>
                </tr>

                <tr class="bg-base-200">
                    <th colspan="2">{{ __('Cost of Goods') }}</th>
                </tr>
                <tr>
                    <td class="pl-8">{{ __('Cost of goods sold') }}</td>
                    <td class="text-right">{{ $rp(-$cogs) }}</td>
                </tr>
                <tr>
                    <td class="pl-8">
                        {{ __('Cost of refunded goods') }}
                        <span class="opacity-60">({{ __(':count refunds, :amount returned to customers', ['count' => $refundCount, 'amount' => $rp($refundAmount)]) }})</span>
                    </td>
                    <td class="text-right">{{ $rp(-$refundCost) }}</td>
                </tr>
                <tr class="font-semibold">
                    <td>{{ __('Gross Profit') }}</td>
                    <td class="text-right">{{ $rp($grossProfit) }}</td>
                </tr>

                <tr class="bg-base-200">
                    <th colspan="2">{{ __('Expenses') }}</th>
                </tr>
                @forelse($expensesByCategory as $expense)
                    <tr>
                        <td class="pl-8">{{ $expense->category }}</td>
                        <td class="text-right">{{ $rp(-$expense->total) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="pl-8 opacity-60" colspan="2">{{ __('No expenses found') }}</td>
                    </tr>
                @endforelse
                <tr class="font-semibold">
                    <td>{{ __('Total Expenses') }}</td>
                    <td class="text-right">{{ $rp(-$totalExpenses) }}</td>
                </tr>

                <tr class="font-bold text-lg bg-base-200">
                    <td>{{ __('Net Profit') }}</td>
                    <td class="text-right {{ $netProfit < 0 ? 'text-error' : 'text-success' }}">{{ $rp($netProfit) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
