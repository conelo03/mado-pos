<div>
    <div class="mb-6">
        <a href="{{ route('products.index') }}" class="btn btn-ghost btn-sm">
            <x-icon.arrow-left />
            Back
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Product Info -->
        <div class="lg:col-span-2 card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">{{ $product->name }}</h2>
                
                <div class="space-y-4">
                    <div>
                        <span class="text-sm opacity-75">Price</span>
                        <div class="text-lg font-semibold">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <span class="text-sm opacity-75">Status</span>
                        <div class="text-lg font-semibold">
                            <div class="badge {{ $product->is_active ? 'badge-success' : 'badge-error' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <a href="{{ route('products.index') }}" class="btn btn-primary w-full">
                    <x-icon.pencil />
                    Edit Product
                </a>
            </div>
        </div>
    </div>

    <!-- BOM Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <h3 class="card-title">Bill of Materials (BOM)</h3>
                <button 
                    wire:click="openBomModal" 
                    class="btn btn-primary btn-sm"
                >
                    <x-icon.plus />
                    Add Material
                </button>
            </div>

            <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Material</th>
                            <th>Unit</th>
                            <th>Qty per Product</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->boms as $bom)
                            <tr class="hover:bg-base-300">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $bom->rawMaterial->name }}</td>
                                <td>{{ $bom->rawMaterial->unit }}</td>
                                <td>{{ $bom->qty }}</td>
                                <td>
                                    <div class="flex gap-1">
                                        <button 
                                            wire:click="editBom({{ $bom->id }})" 
                                            class="btn btn-xs btn-warning" title="Edit"
                                        >
                                            <x-icon.pencil />
                                        </button>
                                        <button 
                                            wire:click="confirmDeleteBom({{ $bom->id }})" 
                                            class="btn btn-xs btn-error" title="Delete"
                                        >
                                            <x-icon.trash />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No materials added</td>
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
                <h3 class="font-bold text-lg mb-4">{{ $editingBomId ? 'Edit' : 'Add' }} Material</h3>
                
                <form wire:submit="saveBom">
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Raw Material</span>
                        </label>
                        <select 
                            wire:model="raw_material_id" 
                            class="select select-bordered w-full"
                        >
                            <option value="">Select material</option>
                            @foreach($rawMaterials as $material)
                                <option value="{{ $material->id }}">{{ $material->name }}</option>
                            @endforeach
                        </select>
                        @error('raw_material_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-6">
                        <label class="label">
                            <span class="label-text">Quantity per Product</span>
                        </label>
                        <input 
                            type="number" 
                            step="0.01"
                            wire:model="qty" 
                            class="input input-bordered w-full"
                        >
                        @error('qty') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="modal-action">
                        <button 
                            type="button"
                            wire:click="closeBomModal" 
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
                <button wire:click="closeBomModal">close</button>
            </form>
        </div>
    @endif

    <!-- Confirm Delete BOM Dialog -->
    <x-confirm-dialog 
        title="Delete BOM"
        message="Are you sure you want to delete this bill of materials? This action cannot be undone."
        confirmText="Delete"
        cancelText="Cancel"
        isDangerous="true"
        onConfirm="$wire.deleteBom({{ $deleteBomId ?? 'null' }})"
    />
</div>
