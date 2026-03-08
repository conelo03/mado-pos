<div>
    <div class="mb-6 flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
        <input 
            type="text" 
            wire:model.live="search" 
            placeholder="Search users..." 
            class="input input-bordered w-full lg:w-64"
        >
        <button 
            wire:click="openModal" 
            class="btn btn-primary w-full lg:w-auto"
        >
            <x-icon.plus />
            Add User
        </button>
    </div>

    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="hover:bg-base-300">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <div class="badge {{ $user->role === 'ADMIN' ? 'badge-primary' : 'badge-secondary' }}">
                                {{ $user->role }}
                            </div>
                        </td>
                        <td>
                            <div class="flex gap-1">
                                <button 
                                    wire:click="edit({{ $user->id }})" 
                                    class="btn btn-xs btn-warning" title="Edit"
                                >
                                    <x-icon.pencil />
                                </button>
                                <button 
                                    wire:click="confirmDelete({{ $user->id }})" 
                                    class="btn btn-xs btn-error" title="Delete"
                                >
                                    <x-icon.trash />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No users found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $users->links('vendor.pagination.tailwind') }}
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">{{ $editingId ? 'Edit' : 'Add' }} User</h3>
                
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
                            <span class="label-text">Email</span>
                        </label>
                        <input 
                            type="email" 
                            wire:model="email" 
                            class="input input-bordered w-full"
                        >
                        @error('email') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">{{ $editingId ? 'Password (leave blank to keep current)' : 'Password' }}</span>
                        </label>
                        <input 
                            type="password" 
                            wire:model="password" 
                            class="input input-bordered w-full"
                        >
                        @error('password') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-6">
                        <label class="label">
                            <span class="label-text">Role</span>
                        </label>
                        <select 
                            wire:model="role" 
                            class="select select-bordered w-full"
                        >
                            <option value="CASHIER">CASHIER</option>
                            <option value="ADMIN">ADMIN</option>
                        </select>
                        @error('role') <span class="text-error text-sm">{{ $message }}</span> @enderror
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
        title="Delete User"
        message="Are you sure you want to delete this user? This action cannot be undone."
        confirmText="Delete"
        cancelText="Cancel"
        isDangerous="true"
        onConfirm="$wire.delete({{ $deleteId ?? 'null' }})"
    />
</div>
