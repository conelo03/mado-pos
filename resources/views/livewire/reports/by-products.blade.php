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
                <span class="label-text">Product</span>
            </label>
            <select 
                wire:model.live="productId" 
                class="select select-bordered w-full"
            >
                <option value="">All Products</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Product Name</th>
                    <th>Qty</th>
                    <th>Cost Subtotal</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report as $index => $item)
                    <tr class="hover:bg-base-300">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['product_name'] }}</td>
                        <td>{{ $item['qty'] }}</td>
                        <td>Rp {{ number_format($item['cost_subtotal'], 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No data found</td>
                    </tr>
                @endforelse
            </tbody>
            @if($report->count() > 0)
                <tfoot>
                    <tr class="font-bold bg-base-200">
                        <td colspan="2">Total</td>
                        <td>{{ $totalQty }}</td>
                        <td>Rp {{ number_format($totalCostSubtotal, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
