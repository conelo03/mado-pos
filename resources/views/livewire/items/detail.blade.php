<div>
    <div class="mb-6">
        <a href="{{ route('items.index') }}" class="btn btn-ghost btn-sm">
            <x-icon.arrow-left />
            {{ __('Back') }}
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="card bg-base-100 shadow-md">
            <div class="card-body">
                <h2 class="card-title">{{ $item->name }}</h2>
                <p class="text-sm text-base-content/60">{{ $item->type === 'PRODUCT' ? __('Product') : __('Raw Material') }}</p>
                <div class="divider my-2"></div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span>{{ __('Unit') }}:</span>
                        <span class="font-semibold">{{ $item->unit }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ __('Cost Price / HPP') }}:</span>
                        <span class="font-semibold">Rp {{ number_format($item->cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ __('Price') }}:</span>
                        <span class="font-semibold">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ __('Stock') }}:</span>
                        <span class="font-semibold">{{ number_format($item->stock, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ __('Min Stock') }}:</span>
                        <span class="font-semibold">{{ number_format($item->minimum_stock, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ __('Track Stock') }}:</span>
                        <span class="badge {{ $item->is_track_stock ? 'badge-success' : 'badge-error' }}">
                            {{ $item->is_track_stock ? __('Active') : __('Inactive') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ __('Status') }}:</span>
                        <span class="badge {{ $item->is_active ? 'badge-success' : 'badge-error' }}">
                            {{ $item->is_active ? __('Active') : __('Inactive') }}
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
                    <h3 class="card-title">{{ __('Bill of Materials (BOM)') }}</h3>
                    <button 
                        wire:click="openBomModal" 
                        class="btn btn-sm btn-primary"
                    >
                        <x-icon.plus />
                        {{ __('Add BOM') }}
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="table table-sm w-full">
                        <thead>
                            <tr>
                                <th>{{ __('Material') }}</th>
                                <th>{{ __('Qty') }}</th>
                                <th>{{ __('Unit') }}</th>
                                <th style="width: 80px;">{{ __('Actions') }}</th>
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
                                    <td colspan="4" class="text-center">{{ __('No BOM items') }}</td>
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
                    <h3 class="font-bold text-lg mb-4">{{ $editingBomId ? __('Edit BOM') : __('Add BOM') }}</h3>
                    
                    <form wire:submit="saveBom">
                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text">{{ __('Material') }}</span>
                            </label>
                            <select wire:model="material_id" class="select select-bordered w-full">
                                <option value="">{{ __('Select Material') }}</option>
                                @foreach(\App\Models\Item::where('type', 'RAW_MATERIAL')->where('is_active', true)->get() as $material)
                                    <option value="{{ $material->id }}">{{ $material->name }}</option>
                                @endforeach
                            </select>
                            @error('material_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text">{{ __('Quantity') }}</span>
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
                            <button type="button" wire:click="closeBomModal" class="btn">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Confirm Dialog for BOM Delete -->
        <x-confirm-dialog 
            :title="__('Delete BOM')"
            :message="__('Are you sure you want to delete this BOM item? This action cannot be undone.')"
            :confirmText="__('Delete')"
            :cancelText="__('Cancel')"
            isDangerous="true"
            onConfirm="$wire.deleteBom({{ $deletingBomId ?? 'null' }})"
        />
    @endif

    <!-- Stock Movements Section -->
    <div class="card bg-base-100 shadow-md">
        <div class="card-body">
            <h3 class="card-title">{{ __('Stock Movements') }}</h3>

            <!-- Filter -->
            <div class="mb-4 flex flex-col lg:flex-row gap-4 items-end">
                <div class="form-control w-full lg:w-auto">
                    <label class="label">
                        <span class="label-text">{{ __('From Date') }}</span>
                    </label>
                    <input 
                        type="date" 
                        wire:model.live="dateFrom" 
                        class="input input-bordered"
                    >
                </div>
                <div class="form-control w-full lg:w-auto">
                    <label class="label">
                        <span class="label-text">{{ __('To Date') }}</span>
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
                    {{ __('Reset') }}
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Qty') }}</th>
                            <th>{{ __('Reference') }}</th>
                            <th>{{ __('Created By') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                            <tr class="hover:bg-base-300">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $movement->created_at->translatedFormat('d M Y H:i') }}</td>
                                <td>
                                    <div class="badge {{ $movement->type === 'PURCHASE' ? 'badge-info' : ($movement->type === 'SALE' ? 'badge-error' : ($movement->type === 'ADJUSTMENT' ? 'badge-success' : 'badge-warning')) }}">
                                        {{ __($movement->type) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="{{ in_array($movement->type, ['OUT']) ? 'text-error font-bold' : 'text-success font-bold' }}">
                                        {{ in_array($movement->type, ['OUT']) ? '-' : '+' }}{{ $movement->qty }}
                                    </span>
                                </td>
                                <td>
                                    @if($movement->reference_type === 'SALE')
                                        {{ __('Sale #:id', ['id' => $movement->reference_id]) }}
                                    @elseif($movement->reference_type === 'SALE_CANCEL')
                                        {{ __('Cancel Sale #:id', ['id' => $movement->reference_id]) }}
                                    @elseif($movement->reference_type === 'PURCHASE')
                                        {{ __('Stock Input') }}
                                    @else
                                        {{ __('Stock Opname') }}
                                    @endif
                                </td>
                                <td>{{ $movement->createdBy?->name ?? __('System') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">{{ __('No movements found') }}</td>
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
