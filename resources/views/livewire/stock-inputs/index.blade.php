<div>
    <div class="mb-6 flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
        <input 
            type="text" 
            wire:model.live="search" 
            placeholder="Search raw material..." 
            class="input input-bordered w-full lg:w-64"
        >
        <button 
            wire:click="openModal" 
            class="btn btn-primary w-full lg:w-auto"
        >
            <x-icon.plus />
            Add Stock Input
        </button>
    </div>

    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Raw Material</th>
                    <th>Qty</th>
                    <th>Date</th>
                    <th>Note</th>
                    <th style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inputs as $input)
                    <tr class="hover:bg-base-300">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $input->rawMaterial->name }}</td>
                        <td>{{ $input->qty }} {{ $input->rawMaterial->unit }}</td>
                        <td>{{ $input->date->format('d M Y') }}</td>
                        <td>{{ $input->note }}</td>
                        <td>
                            <div class="flex gap-1">
                                <button 
                                    wire:click="edit({{ $input->id }})" 
                                    class="btn btn-xs btn-warning" title="Edit"
                                >
                                    <x-icon.pencil />
                                </button>
                                <button 
                                    wire:click="confirmDelete({{ $input->id }})" 
                                    class="btn btn-xs btn-error" title="Delete"
                                >
                                    <x-icon.trash />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No stock inputs found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $inputs->links('vendor.pagination.tailwind') }}
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">{{ $editingId ? 'Edit' : 'Add' }} Stock Input</h3>
                
                <form wire:submit="save">
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Raw Material</span>
                        </label>
                        <select 
                            wire:model="raw_material_id" 
                            class="select select-bordered w-full"
                        >
                            <option value="">Select raw material</option>
                            @foreach($rawMaterials as $material)
                                <option value="{{ $material->id }}">{{ $material->name }}</option>
                            @endforeach
                        </select>
                        @error('raw_material_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Quantity</span>
                        </label>
                        <input 
                            type="number" 
                            step="0.01"
                            wire:model="qty" 
                            class="input input-bordered w-full"
                        >
                        @error('qty') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Date</span>
                        </label>
                        <input 
                            type="date" 
                            wire:model="date" 
                            class="input input-bordered w-full"
                        >
                        @error('date') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-6">
                        <label class="label">
                            <span class="label-text">Note</span>
                        </label>
                        <textarea 
                            wire:model="note" 
                            class="textarea textarea-bordered w-full"
                            rows="3"
                        ></textarea>
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
        title="Delete Stock Input"
        message="Are you sure you want to delete this stock input? This action cannot be undone."
        confirmText="Delete"
        cancelText="Cancel"
        isDangerous="true"
        onConfirm="$wire.delete({{ $deleteId ?? 'null' }})"
    />
</div>
