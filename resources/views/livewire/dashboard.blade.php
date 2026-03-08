<div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <h2 class="card-title text-sm">Total Products</h2>
                    <x-icon.cube class="opacity-20" />
                </div>
                <p class="text-3xl font-bold">{{ $totalProducts }}</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <h2 class="card-title text-sm">Raw Materials</h2>
                    <x-icon.check-circle class="opacity-20" />
                </div>
                <p class="text-3xl font-bold">{{ $totalRawMaterials }}</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <h2 class="card-title text-sm">Today Sales</h2>
                    <x-icon.bolt class="opacity-20" />
                </div>
                <p class="text-3xl font-bold">{{ $todaySales }}</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <h2 class="card-title text-sm">Today Revenue</h2>
                    <x-icon.currency-dollar class="opacity-20" />
                </div>
                <p class="text-3xl font-bold">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h3 class="card-title">
                <x-icon.document-text />
                Recent Transactions
            </h3>
            <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Invoice</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $sale)
                            <tr class="hover:bg-base-300">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $sale->invoice_no }}</td>
                                <td>Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                                <td>
                                    <div class="badge {{ $sale->status === 'PAID' ? 'badge-success' : ($sale->status === 'VOID' ? 'badge-error' : 'badge-warning') }}">
                                        {{ $sale->status }}
                                    </div>
                                </td>
                                <td>{{ $sale->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No transactions yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
