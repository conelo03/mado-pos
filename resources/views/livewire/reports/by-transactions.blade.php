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
    </div>

    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Invoice</th>
                    <th>Date</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr class="hover:bg-base-300">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $sale->invoice_no }}</td>
                        <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                        <td>Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No data found</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="font-bold bg-base-200">
                    <td colspan="3">Total Revenue</td>
                    <td>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-6">
        {{ $sales->links('vendor.pagination.tailwind') }}
    </div>
</div>
