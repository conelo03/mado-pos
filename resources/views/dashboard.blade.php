<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <h2 class="card-title text-sm">Total Products</h2>
                    <x-icon.cube class="opacity-20" />
                </div>
                <p class="text-3xl font-bold">{{ \App\Models\Product::count() }}</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <h2 class="card-title text-sm">Raw Materials</h2>
                    <x-icon.check-circle class="opacity-20" />
                </div>
                <p class="text-3xl font-bold">{{ \App\Models\RawMaterial::count() }}</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <h2 class="card-title text-sm">Today Sales</h2>
                    <x-icon.bolt class="opacity-20" />
                </div>
                <p class="text-3xl font-bold">{{ \App\Models\Sale::whereDate('created_at', today())->count() }}</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <h2 class="card-title text-sm">Today Revenue</h2>
                    <x-icon.currency-dollar class="opacity-20" />
                </div>
                <p class="text-3xl font-bold">Rp {{ number_format(\App\Models\Sale::whereDate('created_at', today())->sum('total_price'), 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h3 class="card-title">
                <x-icon.document-text />
                Recent Transactions
            </h3>
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(\App\Models\Sale::latest()->limit(10)->get() as $sale)
                            <tr>
                                <td>{{ $sale->invoice_no }}</td>
                                <td>Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                                <td>
                                    <div class="badge badge-success">
                                        {{ $sale->status }}
                                    </div>
                                </td>
                                <td>{{ $sale->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No transactions yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
