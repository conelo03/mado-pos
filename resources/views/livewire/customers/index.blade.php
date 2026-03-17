<div>
    <div class="mb-6 flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
        <div class="flex gap-2 w-full lg:w-auto">
            <input 
                type="text" 
                wire:model.live="search" 
                placeholder="Search customers..." 
                class="input input-bordered flex-1 lg:flex-none lg:w-64"
            >
            <select 
                wire:model.live="priceListTypeFilter"
                class="select select-bordered"
            >
                <option value="">All Price Types</option>
                @foreach($priceListTypes as $priceListType)
                    <option value="{{ $priceListType->id }}">{{ $priceListType->name }}</option>
                @endforeach
            </select>
        </div>
        <button 
            wire:click="openModal" 
            class="btn btn-primary w-full lg:w-auto"
        >
            <x-icon.plus />
            Add Customer
        </button>
    </div>

    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Name</th>
                    <th>Price List Type</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr class="hover:bg-base-300">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>
                            <div class="badge {{ $customer->priceListType->type === 'RETAIL' ? 'badge-info' : ($customer->priceListType->type === 'GROSIR' ? 'badge-warning' : ($customer->priceListType->type === 'MEMBER' ? 'badge-success' : 'badge-primary')) }}">
                                {{ $customer->priceListType->name }}
                            </div>
                        </td>
                        <td>{{ $customer->phone ?? '-' }}</td>
                        <td>{{ $customer->address ?? '-' }}</td>
                        <td>
                            <div class="flex gap-1">
                                <button 
                                    wire:click="edit({{ $customer->id }})" 
                                    class="btn btn-xs btn-warning" title="Edit"
                                >
                                    <x-icon.pencil />
                                </button>
                                <button 
                                    wire:click="confirmDelete({{ $customer->id }})" 
                                    class="btn btn-xs btn-error" title="Delete"
                                >
                                    <x-icon.trash />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No customers found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $customers->links('vendor.pagination.tailwind') }}
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">{{ $editingId ? 'Edit' : 'Add' }} Customer</h3>
                
                <form wire:submit="save">
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Price List Type</span>
                        </label>
                        <select wire:model="price_list_type_id" class="select select-bordered w-full">
                            <option value="">Select Price List Type</option>
                            @foreach($priceListTypes as $priceListType)
                                <option value="{{ $priceListType->id }}">{{ $priceListType->name }} ({{ $priceListType->type }})</option>
                            @endforeach
                        </select>
                        @error('price_list_type_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

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
                            <span class="label-text">Phone</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="phone" 
                            class="input input-bordered w-full"
                            placeholder="Optional"
                        >
                        @error('phone') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-6">
                        <label class="label">
                            <span class="label-text">Address</span>
                        </label>
                        <textarea 
                            wire:model="address" 
                            rows="3"
                            class="textarea textarea-bordered w-full"
                            placeholder="Optional address..."
                        ></textarea>
                        @error('address') <span class="text-error text-sm">{{ $message }}</span> @enderror
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
        title="Delete Customer"
        message="Are you sure you want to delete this customer? This action cannot be undone."
        confirmText="Delete"
        cancelText="Cancel"
        isDangerous="true"
        onConfirm="$wire.delete({{ $deleteId ?? 'null' }})"
    />
</div>