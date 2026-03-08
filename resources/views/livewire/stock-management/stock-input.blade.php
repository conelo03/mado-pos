<div>
    <div class="mb-6 flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
        <input 
            type="text" 
            wire:model.live="search" 
            placeholder="Search item..." 
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
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Date</th>
                    <th>Note</th>
                    <th style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                    <tr class="hover:bg-base-300">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $movement->item->name }}</td>
                        <td>{{ number_format($movement->qty, 2, ',', '.') }} {{ $movement->item->unit }}</td>
                        <td>{{ $movement->date->format('d M Y') }}</td>
                        <td>{{ $movement->note ?? '-' }}</td>
                        <td>
                            <div class="flex gap-1">
                                <button 
                                    wire:click="edit({{ $movement->id }})" 
                                    class="btn btn-xs btn-warning" title="Edit"
                                >
                                    <x-icon.pencil />
                                </button>
                                <button 
                                    wire:click="confirmDelete({{ $movement->id }})" 
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

    <div class="mt-4">
        {{ $movements->links('vendor.pagination.tailwind') }}
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">{{ $editingId ? 'Edit' : 'Add' }} Stock Input</h3>
                
                <form wire:submit="save">
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Item</span>
                        </label>
                        <select 
                            wire:model="item_id" 
                            class="select select-bordered w-full"
                        >
                            <option value="">Select item</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                        @error('item_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Quantity</span>
                        </label>
                        <input 
                            type="number" 
                            step="1"
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

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Note</span>
                        </label>
                        <textarea 
                            wire:model="note" 
                            class="textarea textarea-bordered w-full"
                            rows="3"
                        ></textarea>
                        @error('note') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="modal-action">
                        <button type="button" wire:click="closeModal" class="btn">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
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
