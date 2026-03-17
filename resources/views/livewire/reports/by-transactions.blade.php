<div>
    <div class="mb-6 flex flex-col lg:flex-row gap-4 items-end">
        <div class="flex-1">
            <label class="label">
                <span class="label-text">Start Date</span>
            </label>
            <input 
                type="date" 
                wire:model.live="startDate" 
                class="input input-bordered w-full"
            >
        </div>
        <div class="flex-1">
            <label class="label">
                <span class="label-text">End Date</span>
            </label>
            <input 
                type="date" 
                wire:model.live="endDate" 
                class="input input-bordered w-full"
            >
        </div>
        <div class="flex-1">
            <label class="label">
                <span class="label-text">Status</span>
            </label>
            <select 
                wire:model.live="status" 
                class="select select-bordered w-full"
            >
                <option value="PAID">PAID</option>
                <option value="REFUND">REFUND</option>
                <option value="">All Status</option>
            </select>
        </div>
        <div class="flex-1">
            <label class="label">
                <span class="label-text">Customer</span>
            </label>
            <select 
                wire:model.live="customerFilter" 
                class="select select-bordered w-full"
            >
                <option value="">All Customers</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label class="label">
                <span class="label-text">Price Type</span>
            </label>
            <select 
                wire:model.live="priceListTypeFilter" 
                class="select select-bordered w-full"
            >
                <option value="">All Price Types</option>
                @foreach($priceListTypes as $priceType)
                    <option value="{{ $priceType->id }}">{{ $priceType->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Invoice</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Price Type</th>
                    <th>Status</th>
                    <th>Total Cost</th>
                    <th>Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr class="hover:bg-base-300">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $sale->invoice_no }}</td>
                        <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $sale->customer?->name ?? 'Walk-in Customer' }}</td>
                        <td>{{ $sale->priceListType?->name ?? '-' }}</td>
                        <td>
                            <div class="badge {{ $sale->status === 'PAID' ? 'badge-success' : ($sale->status === 'REFUND' ? 'badge-warning' : 'badge-error') }}">
                                {{ $sale->status }}
                            </div>
                        </td>
                        <td>Rp {{ number_format($sale->total_cost, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No data found</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="font-bold bg-base-200">
                    <td colspan="6">Total</td>
                    <td>Rp {{ number_format($totalCost, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-6">
        {{ $sales->links('vendor.pagination.tailwind') }}
    </div>
</div>
