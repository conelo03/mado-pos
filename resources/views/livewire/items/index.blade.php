<div>
    <div class="mb-6 flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
        <div class="flex gap-2 w-full lg:w-auto">
            <input 
                type="text" 
                wire:model.live="search" 
                placeholder="Search items..." 
                class="input input-bordered flex-1 lg:flex-none lg:w-64"
            >
            <select 
                wire:model.live="typeFilter"
                class="select select-bordered"
            >
                <option value="">All Types</option>
                <option value="PRODUCT">Product</option>
                <option value="RAW_MATERIAL">Raw Material</option>
            </select>
        </div>
        <button 
            wire:click="openModal" 
            class="btn btn-primary w-full lg:w-auto"
        >
            <x-icon.plus />
            Add Item
        </button>
    </div>

    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Unit</th>
                    <th>Cost Price / HPP</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Min Stock</th>
                    <th>Track Stock</th>
                    <th>Status</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="hover:bg-base-300">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->name }}</td>
                        <td>
                            <span class="badge {{ $item->type === 'PRODUCT' ? 'badge-info' : 'badge-warning' }}">
                                {{ $item->type === 'PRODUCT' ? 'Product' : 'Raw Material' }}
                            </span>
                        </td>
                        <td>{{ $item->unit }}</td>
                        <td>Rp {{ number_format($item->cost, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>{{ number_format($item->stock, 2, ',', '.') }}</td>
                        <td>{{ number_format($item->minimum_stock, 2, ',', '.') }}</td><td>
                            <div class="badge {{ $item->is_track_stock ? 'badge-success' : 'badge-error' }}">
                                {{ $item->is_track_stock ? 'Active' : 'Inactive' }}
                            </div>
                        </td>
                        <td>
                            <div class="badge {{ $item->is_active ? 'badge-success' : 'badge-error' }}">
                                {{ $item->is_active ? 'Active' : 'Inactive' }}
                            </div>
                        </td>
                        <td>
                            <div class="flex gap-1">
                                <a href="{{ route('items.detail', $item->id) }}" class="btn btn-xs btn-info" title="Detail">
                                    <x-icon.eye />
                                </a>
                                <button 
                                    wire:click="edit({{ $item->id }})" 
                                    class="btn btn-xs btn-warning" title="Edit"
                                >
                                    <x-icon.pencil />
                                </button>
                                <button 
                                    wire:click="confirmDelete({{ $item->id }})" 
                                    class="btn btn-xs btn-error" title="Delete"
                                >
                                    <x-icon.trash />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No items found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $items->links('vendor.pagination.tailwind') }}
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">{{ $editingId ? 'Edit' : 'Add' }} Item</h3>
                
                <form wire:submit="save">
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Name</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="name" 
                            class="input input-bordered w-full"
                        >
                        @error('name') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Type</span>
                        </label>
                        <select wire:model="type" class="select select-bordered w-full">
                            <option value="PRODUCT">Product</option>
                            <option value="RAW_MATERIAL">Raw Material</option>
                        </select>
                        @error('type') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Unit</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="unit" 
                            class="input input-bordered w-full"
                            placeholder="e.g., pcs, kg, liter"
                        >
                        @error('unit') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Cost Price / HPP</span>
                        </label>
                        <input 
                            type="number" 
                            step="1"
                            wire:model="cost" 
                            class="input input-bordered w-full"
                        >
                        @error('cost') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Price</span>
                        </label>
                        <input 
                            type="number" 
                            step="1"
                            wire:model="price" 
                            class="input input-bordered w-full"
                        >
                        @error('price') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Minimum Stock</span>
                        </label>
                        <input 
                            type="number" 
                            step="1"
                            wire:model="minimum_stock" 
                            class="input input-bordered w-full"
                        >
                        @error('minimum_stock') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label cursor-pointer">
                            <span class="label-text">Active</span>
                            <input 
                                type="checkbox" 
                                wire:model="is_active" 
                                class="checkbox"
                            >
                        </label>
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label cursor-pointer">
                            <span class="label-text">Track Stock</span>
                            <input 
                                type="checkbox" 
                                wire:model="is_track_stock" 
                                class="checkbox"
                            >
                        </label>
                    </div>

                    <div class="modal-action">
                        <button type="button" wire:click="closeModal" class="btn">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <x-confirm-dialog 
        title="Delete Item"
        message="Are you sure you want to delete this item? This action cannot be undone."
        confirmText="Delete"
        cancelText="Cancel"
        isDangerous="true"
        onConfirm="$wire.delete({{ $deleteId ?? 'null' }})"
    />
</div>
