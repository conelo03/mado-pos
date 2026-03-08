<div>
    <div class="mb-6 flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
        <input 
            type="text" 
            wire:model.live="search" 
            placeholder="Search products..." 
            class="input input-bordered w-full lg:w-64"
        >
        <button 
            wire:click="openModal" 
            class="btn btn-primary w-full lg:w-auto"
        >
            <x-icon.plus />
            Add Product
        </button>
    </div>

    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="hover:bg-base-300">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $product->name }}</td>
                        <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td>
                            <div class="badge {{ $product->is_active ? 'badge-success' : 'badge-error' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </div>
                        </td>
                        <td>
                            <div class="flex gap-1">
                                <a href="{{ route('products.detail', $product->id) }}" class="btn btn-xs btn-info" title="Detail">
                                    <x-icon.eye />
                                </a>
                                <button 
                                    wire:click="edit({{ $product->id }})" 
                                    class="btn btn-xs btn-warning" title="Edit"
                                >
                                    <x-icon.pencil />
                                </button>
                                <button 
                                    wire:click="confirmDelete({{ $product->id }})" 
                                    class="btn btn-xs btn-error" title="Delete"
                                >
                                    <x-icon.trash />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No products found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $products->links('vendor.pagination.tailwind') }}
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">{{ $editingId ? 'Edit' : 'Add' }} Product</h3>
                
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
                            <span class="label-text">Price</span>
                        </label>
                        <input 
                            type="number" 
                            step="0.01"
                            wire:model="price" 
                            class="input input-bordered w-full"
                        >
                        @error('price') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control mb-6">
                        <label class="label cursor-pointer">
                            <span class="label-text">Active</span>
                            <input 
                                type="checkbox" 
                                wire:model.boolean="is_active"
                                value="1"
                                class="checkbox"
                            >
                        </label>
                    </div>

                    <div class="modal-action">
                        <button 
                            type="button"
                            wire:click="closeModal" 
                            class="btn"
                        >
                            <x-icon.x />
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="btn btn-primary"
                        >
                            <x-icon.check />
                            Save
                        </button>
                    </div>
                </form>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button wire:click="closeModal">close</button>
            </form>
        </div>
    @endif

    <!-- Confirm Delete Dialog -->
    <x-confirm-dialog 
        title="Delete Product"
        message="Are you sure you want to delete this product? This action cannot be undone."
        confirmText="Delete"
        cancelText="Cancel"
        isDangerous="true"
        onConfirm="$wire.delete({{ $deleteId ?? 'null' }})"
    />
</div>
