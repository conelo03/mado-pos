<div>
    <div class="mb-6">
        <a href="{{ route('items.index') }}" class="btn btn-ghost btn-sm">
            <x-icon.arrow-left />
            Back
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="card bg-base-100 shadow-md">
            <div class="card-body">
                <h2 class="card-title">{{ $item->name }}</h2>
                <p class="text-sm text-base-content/60">{{ $item->type === 'PRODUCT' ? 'Product' : 'Raw Material' }}</p>
                <div class="divider my-2"></div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span>Unit:</span>
                        <span class="font-semibold">{{ $item->unit }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Cost Price / HPP:</span>
                        <span class="font-semibold">Rp {{ number_format($item->cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Price:</span>
                        <span class="font-semibold">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Stock:</span>
                        <span class="font-semibold">{{ number_format($item->stock, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Min Stock:</span>
                        <span class="font-semibold">{{ number_format($item->minimum_stock, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Track Stock:</span>
                        <span class="badge {{ $item->is_track_stock ? 'badge-success' : 'badge-error' }}">
                            {{ $item->is_track_stock ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span>Status:</span>
                        <span class="badge {{ $item->is_active ? 'badge-success' : 'badge-error' }}">
                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if($item->type === 'PRODUCT')
            <div class="lg:col-span-2">
                @livewire('items.price-list-manager', ['itemId' => $item->id])
            </div>
        @endif
    </div>

    @if($item->type === 'PRODUCT')
        <!-- BOM Section -->
        <div class="card bg-base-100 shadow-md mb-6">
            <div class="card-body">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="card-title">Bill of Materials (BOM)</h3>
                    <button 
                        wire:click="openBomModal" 
                        class="btn btn-sm btn-primary"
                    >
                        <x-icon.plus />
                        Add BOM
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="table table-sm w-full">
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th style="width: 80px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($boms as $bom)
                                <tr>
                                    <td>{{ $bom->material->name }}</td>
                                    <td>{{ number_format($bom->qty, 2, ',', '.') }}</td>
                                    <td>{{ $bom->material->unit }}</td>
                                    <td>
                                        <div class="flex gap-1">
                                            <button 
                                                wire:click="editBom({{ $bom->id }})" 
                                                class="btn btn-xs btn-warning"
                                            >
                                                <x-icon.pencil />
                                            </button>
                                            <button 
                                                wire:click="confirmDeleteBom({{ $bom->id }})" 
                                                class="btn btn-xs btn-error"
                                            >
                                                <x-icon.trash />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No BOM items</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- BOM Modal -->
        @if($showBomModal)
            <div class="modal modal-open">
                <div class="modal-box">
                    <h3 class="font-bold text-lg mb-4">{{ $editingBomId ? 'Edit' : 'Add' }} BOM</h3>
                    
                    <form wire:submit="saveBom">
                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text">Material</span>
                            </label>
                            <select wire:model="material_id" class="select select-bordered w-full">
                                <option value="">Select Material</option>
                                @foreach(\App\Models\Item::where('type', 'RAW_MATERIAL')->where('is_active', true)->get() as $material)
                                    <option value="{{ $material->id }}">{{ $material->name }}</option>
                                @endforeach
                            </select>
                            @error('material_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text">Quantity</span>
                            </label>
                            <input 
                                type="number" 
                                step="1"
                                wire:model="bom_qty" 
                                class="input input-bordered w-full"
                            >
                            @error('bom_qty') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="modal-action">
                            <button type="button" wire:click="closeBomModal" class="btn">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Confirm Dialog for BOM Delete -->
        <x-confirm-dialog 
            title="Delete BOM"
            message="Are you sure you want to delete this BOM item? This action cannot be undone."
            confirmText="Delete"
            cancelText="Cancel"
            isDangerous="true"
            onConfirm="$wire.deleteBom({{ $deletingBomId ?? 'null' }})"
        />
    @endif

    <!-- Stock Movements Section -->
    <div class="card bg-base-100 shadow-md">
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
                        wire:model.live="dateFrom" 
                        class="input input-bordered"
                    >
                </div>
                <div class="form-control w-full lg:w-auto">
                    <label class="label">
                        <span class="label-text">To Date</span>
                    </label>
                    <input 
                        type="date" 
                        wire:model.live="dateTo" 
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
                                    <span class="{{ in_array($movement->type, ['OUT']) ? 'text-error font-bold' : 'text-success font-bold' }}">
                                        {{ in_array($movement->type, ['OUT']) ? '-' : '+' }}{{ $movement->qty }}
                                    </span>
                                </td>
                                <td>
                                    @if($movement->reference_type === 'SALE')
                                        Sale #{{ $movement->reference_id }}
                                    @elseif($movement->reference_type === 'PURCHASE')
                                        Stock Input
                                    @else
                                        Stock Opname
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
