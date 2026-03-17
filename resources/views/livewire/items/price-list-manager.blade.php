<div>
    <div class="card bg-base-100 shadow-md">
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <h3 class="card-title">Price Lists</h3>
                @if($availablePriceListTypes->count() > 0)
                    <button 
                        wire:click="openModal" 
                        class="btn btn-sm btn-primary"
                    >
                        <x-icon.plus />
                        Add Price
                    </button>
                @endif
            </div>

            @if($priceLists->count() > 0)
                <div class="overflow-x-auto rounded-box border border-base-content/5">
                    <table class="table table-sm w-full">
                        <thead>
                            <tr>
                                <th>Price List Type</th>
                                <th>Price</th>
                                <th style="width: 80px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($priceLists as $priceList)
                                <tr class="hover:bg-base-300">
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <span class="badge {{ $priceList->priceListType->type === 'RETAIL' ? 'badge-info' : ($priceList->priceListType->type === 'GROSIR' ? 'badge-warning' : ($priceList->priceListType->type === 'MEMBER' ? 'badge-success' : 'badge-primary')) }}">
                                                {{ $priceList->priceListType->type }}
                                            </span>
                                            <span>{{ $priceList->priceListType->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="font-bold text-lg">Rp {{ number_format($priceList->price, 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        <div class="flex gap-1">
                                            <button 
                                                wire:click="edit({{ $priceList->id }})" 
                                                class="btn btn-xs btn-warning" title="Edit"
                                            >
                                                <x-icon.pencil />
                                            </button>
                                            <button 
                                                wire:click="confirmDelete({{ $priceList->id }})" 
                                                class="btn btn-xs btn-error" title="Delete"
                                            >
                                                <x-icon.trash />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-base-content/60">
                    <p>No price lists configured for this item.</p>
                    @if($availablePriceListTypes->count() > 0)
                        <button 
                            wire:click="openModal" 
                            class="btn btn-sm btn-ghost mt-2"
                        >
                            Add the first price list
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">{{ $editingId ? 'Edit' : 'Add' }} Price List</h3>
                
                <form wire:submit="save">
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Price List Type</span>
                        </label>
                        <select wire:model="price_list_type_id" class="select select-bordered w-full">
                            <option value="">Select Price List Type</option>
                            @if($editingId)
                                @foreach($allPriceListTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->type }})</option>
                                @endforeach
                            @else
                                @foreach($availablePriceListTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->type }})</option>
                                @endforeach
                            @endif
                        </select>
                        @error('price_list_type_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-6">
                        <label class="label">
                            <span class="label-text">Price</span>
                        </label>
                        <input 
                            wire:model="price" 
                            type="number" 
                            step="1" 
                            min="0"
                            class="input input-bordered w-full"
                        >
                        @error('price') <span class="text-error text-sm">{{ $message }}</span> @enderror
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
    <x-confirm-price-list-delete-dialog 
        title="Delete Price List"
        message="Are you sure you want to delete this price list? This action cannot be undone."
        confirmText="Delete"
        cancelText="Cancel"
        isDangerous="true"
        onConfirm="$wire.delete({{ $deleteId ?? 'null' }})"
    />
</div>