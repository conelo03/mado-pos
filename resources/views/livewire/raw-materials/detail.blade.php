<div>
    <div class="mb-6">
        <a href="{{ route('raw-materials.index') }}" class="btn btn-ghost btn-sm">
            <x-icon.arrow-left />
            Back
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Material Info -->
        <div class="lg:col-span-2 card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">{{ $material->name }}</h2>
                
                <div class="space-y-4">
                    <div>
                        <span class="text-sm opacity-75">Unit</span>
                        <div class="text-lg font-semibold">{{ $material->unit }}</div>
                    </div>
                    <div>
                        <span class="text-sm opacity-75">Current Stock</span>
                        <div class="text-lg font-semibold">{{ $material->stock }}</div>
                    </div>
                    <div>
                        <span class="text-sm opacity-75">Minimum Stock</span>
                        <div class="text-lg font-semibold">{{ $material->minimum_stock }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body items-center text-center">
                @if($material->stock < $material->minimum_stock)
                    <div class="badge badge-lg badge-error">
                        <x-icon.exclamation-triangle />
                        Low Stock
                    </div>
                @else
                    <div class="badge badge-lg badge-success">
                        <x-icon.check-circle />
                        Stock OK
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Stock Movements -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h3 class="card-title">Stock Movements</h3>

            <!-- Filter -->
            <div class="mb-4 flex flex-col lg:flex-row gap-4 items-end">
                <div class="form-control w-full lg:w-auto">
                    <label class="label">
                        <span class="label-text">From Date</span>
                    </label>
                    <input 
                        type="date" 
                        wire:model="dateFrom" 
                        class="input input-bordered"
                    >
                </div>
                <div class="form-control w-full lg:w-auto">
                    <label class="label">
                        <span class="label-text">To Date</span>
                    </label>
                    <input 
                        type="date" 
                        wire:model="dateTo" 
                        class="input input-bordered"
                    >
                </div>
                <button 
                    wire:click="resetFilter" 
                    class="btn btn-outline"
                >
                    <x-icon.arrow-path />
                    Reset
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Qty</th>
                            <th>Reference</th>
                            <th>Created By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                            <tr class="hover:bg-base-300">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $movement->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <div class="badge {{ $movement->type === 'PURCHASE' ? 'badge-info' : ($movement->type === 'SALE' ? 'badge-error' : ($movement->type === 'ADJUSTMENT' ? 'badge-success' : 'badge-warning')) }}">
                                        {{ $movement->type }}
                                    </div>
                                </td>
                                <td>
                                    <span class="{{ in_array($movement->type, ['SALE', 'WASTE']) ? 'text-error font-bold' : 'text-success font-bold' }}">
                                        {{ in_array($movement->type, ['SALE', 'WASTE']) ? '-' : '+' }}{{ $movement->qty }}
                                    </span>
                                </td>
                                <td>
                                    @if($movement->reference_type === 'SALE')
                                        Sale #{{ $movement->reference_id }}
                                    @elseif($movement->reference_type === 'PURCHASE')
                                        Stock Input #{{ $movement->reference_id }}
                                    @else
                                        {{ $movement->reference_type }} #{{ $movement->reference_id }}
                                    @endif
                                </td>
                                <td>{{ $movement->createdBy?->name ?? 'System' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No movements found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $movements->links('vendor.pagination.tailwind') }}
            </div>
        </div>
    </div>
</div>
