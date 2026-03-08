<div>
    <div class="mb-6 flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
        <input 
            type="text" 
            wire:model.live="search" 
            placeholder="Search raw materials..." 
            class="input input-bordered w-full lg:w-64"
        >
        <button 
            wire:click="openModal" 
            class="btn btn-primary w-full lg:w-auto"
        >
            <x-icon.plus />
            Add Raw Material
        </button>
    </div>

    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Name</th>
                    <th>Unit</th>
                    <th>Stock</th>
                    <th>Min Stock</th>
                    <th style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rawMaterials as $material)
                    <tr class="hover:bg-base-300">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $material->name }}</td>
                        <td>{{ $material->unit }}</td>
                        <td>{{ $material->stock }}</td>
                        <td>{{ $material->minimum_stock }}</td>
                        <td>
                            <div class="flex gap-1">
                                <a href="{{ route('raw-materials.detail', $material->id) }}" class="btn btn-xs btn-info" title="Detail">
                                    <x-icon.eye />
                                </a>
                                <button 
                                    wire:click="edit({{ $material->id }})" 
                                    class="btn btn-xs btn-warning" title="Edit"
                                >
                                    <x-icon.pencil />
                                </button>
                                <button 
                                    wire:click="confirmDelete({{ $material->id }})" 
                                    class="btn btn-xs btn-error" title="Delete"
                                >
                                    <x-icon.trash />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No raw materials found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $rawMaterials->links('vendor.pagination.tailwind') }}
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">{{ $editingId ? 'Edit' : 'Add' }} Raw Material</h3>
                
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
                            <span class="label-text">Unit</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="unit" 
                            placeholder="e.g., kg, liter, pcs"
                            class="input input-bordered w-full"
                        >
                        @error('unit') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-6">
                        <label class="label">
                            <span class="label-text">Minimum Stock</span>
                        </label>
                        <input 
                            type="number" 
                            step="0.01"
                            wire:model="minimum_stock" 
                            class="input input-bordered w-full"
                        >
                        @error('minimum_stock') <span class="text-error text-sm">{{ $message }}</span> @enderror
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
        title="Delete Raw Material"
        message="Are you sure you want to delete this raw material? This action cannot be undone."
        confirmText="Delete"
        cancelText="Cancel"
        isDangerous="true"
        onConfirm="$wire.delete({{ $deleteId ?? 'null' }})"
    />
</div>
