<div>
    <div class="mb-6 flex flex-col lg:flex-row gap-4 items-end">
        <div class="flex-1">
            <label class="label">
                <span class="label-text">{{ __('Start Date') }}</span>
            </label>
            <input 
                type="date" 
                wire:model.live="startDate" 
                class="input input-bordered w-full"
            >
        </div>
        <div class="flex-1">
            <label class="label">
                <span class="label-text">{{ __('End Date') }}</span>
            </label>
            <input 
                type="date" 
                wire:model.live="endDate" 
                class="input input-bordered w-full"
            >
        </div>
        <div class="flex-1">
            <label class="label">
                <span class="label-text">{{ __('Status') }}</span>
            </label>
            <select 
                wire:model.live="status" 
                class="select select-bordered w-full"
            >
                <option value="PAID">{{ __('PAID') }}</option>
                <option value="REFUND">{{ __('REFUND') }}</option>
                <option value="">{{ __('All Status') }}</option>
            </select>
        </div>
        <div class="flex-1">
            <label class="label">
                <span class="label-text">{{ __('Customer') }}</span>
            </label>
            <select 
                wire:model.live="customerFilter" 
                class="select select-bordered w-full"
            >
                <option value="">{{ __('All Customers') }}</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label class="label">
                <span class="label-text">{{ __('Price Type') }}</span>
            </label>
            <select 
                wire:model.live="priceListTypeFilter" 
                class="select select-bordered w-full"
            >
                <option value="">{{ __('All Price Types') }}</option>
                @foreach($priceListTypes as $priceType)
                    <option value="{{ $priceType->id }}">{{ $priceType->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label class="label">
                <span class="label-text">{{ __('Payment') }}</span>
            </label>
            <select 
                wire:model.live="paymentMethodFilter" 
                class="select select-bordered w-full"
            >
                <option value="">{{ __('All Payments') }}</option>
                @foreach (['CASH', 'NON_CASH'] as $method)
                    <option value="{{ $method }}">{{ \App\Models\Sale::paymentMethodLabel($method) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>{{ __('Invoice') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Customer') }}</th>
                    <th>{{ __('Price Type') }}</th>
                    <th>{{ __('Payment') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Total Cost') }}</th>
                    <th>{{ __('Total Revenue') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr class="hover:bg-base-300">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $sale->invoice_no }}</td>
                        <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $sale->customer?->name ?? __('Walk-in Customer') }}</td>
                        <td>{{ $sale->priceListType?->name ?? '-' }}</td>
                        <td>{{ \App\Models\Sale::paymentMethodLabel($sale->payment_method) }}</td>
                        <td>
                            <div class="badge {{ $sale->status === 'PAID' ? 'badge-success' : ($sale->status === 'REFUND' ? 'badge-warning' : 'badge-error') }}">
                                {{ __($sale->status) }}
                            </div>
                        </td>
                        <td>Rp {{ number_format($sale->total_cost, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">{{ __('No data found') }}</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="font-bold bg-base-200">
                    <td colspan="7">{{ __('Total') }}</td>
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
