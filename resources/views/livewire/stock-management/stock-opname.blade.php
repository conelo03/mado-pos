<div>
    <div class="mb-6 flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
        <input 
            type="text" 
            wire:model.live="search" 
            placeholder="{{ __('Search item...') }}" 
            class="input input-bordered w-full lg:w-64"
        >
        <button 
            wire:click="openModal" 
            class="btn btn-primary w-full lg:w-auto"
        >
            <x-icon.plus />
            {{ __('Add Stock Opname') }}
        </button>
    </div>

    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>{{ __('Item') }}</th>
                    <th>{{ __('Qty') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Note') }}</th>
                    <th style="width: 100px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                    <tr class="hover:bg-base-300">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $movement->item->name }}</td>
                        <td>
                            <span class="{{ $movement->reference_type === 'WASTE' ? 'text-error font-bold' : 'text-success font-bold' }}">
                                {{ $movement->reference_type === 'WASTE' ? '-' : '+' }}{{ number_format($movement->qty, 2, ',', '.') }}
                            </span>
                            {{ $movement->item->unit }}
                        </td>
                        <td>
                            <div class="badge {{ $movement->reference_type === 'ADJUSTMENT' ? 'badge-success' : 'badge-warning' }}">
                                {{ __($movement->reference_type) }}
                            </div>
                        </td>
                        <td>{{ $movement->date->translatedFormat('d M Y') }}</td>
                        <td>{{ $movement->note ?? '-' }}</td>
                        <td>
                            <div class="flex gap-1">
                                <button 
                                    wire:click="edit({{ $movement->id }})" 
                                    class="btn btn-xs btn-warning" title="{{ __('Edit') }}"
                                >
                                    <x-icon.pencil />
                                </button>
                                <button 
                                    wire:click="confirmDelete({{ $movement->id }})" 
                                    class="btn btn-xs btn-error" title="{{ __('Delete') }}"
                                >
                                    <x-icon.trash />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">{{ __('No stock opnames found') }}</td>
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
            <div class="modal-box overflow-visible">
                <h3 class="font-bold text-lg mb-4">{{ $editingId ? __('Edit Stock Opname') : __('Add Stock Opname') }}</h3>
                
                <form wire:submit="save">
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">{{ __('Item') }}</span>
                        </label>
                        <div
                            x-data="{
                                open: false,
                                search: '',
                                selectedLabel: '',
                                items: {{ Js::from($items->map(fn($i) => ['id' => $i->id, 'name' => $i->name])) }},
                                get filtered() {
                                    if (!this.search) return this.items;
                                    return this.items.filter(i => i.name.toLowerCase().includes(this.search.toLowerCase()));
                                },
                                select(item) {
                                    this.selectedLabel = item.name;
                                    this.search = '';
                                    this.open = false;
                                    $wire.set('item_id', item.id);
                                },
                                init() {
                                    const current = {{ $item_id ?: 'null' }};
                                    if (current) {
                                        const found = this.items.find(i => i.id == current);
                                        if (found) this.selectedLabel = found.name;
                                    }
                                }
                            }"
                            class="relative"
                        >
                            <button
                                type="button"
                                @click="open = !open"
                                class="select select-bordered w-full text-left flex items-center justify-between"
                            >
                                <span x-text="selectedLabel || {{ Js::from(__('Select item...')) }}" :class="selectedLabel ? '' : 'opacity-50'"></span>
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div
                                x-show="open"
                                x-transition
                                @click.outside="open = false"
                                class="absolute z-50 w-full mt-1 bg-base-100 border border-base-300 rounded-box shadow-lg"
                            >
                                <div class="p-2 border-b border-base-300">
                                    <input
                                        type="text"
                                        x-model="search"
                                        x-ref="searchInput"
                                        @click.stop
                                        placeholder="{{ __('Search...') }}"
                                        class="input input-sm input-bordered w-full"
                                        x-init="$watch('open', val => val && $nextTick(() => $refs.searchInput.focus()))"
                                    >
                                </div>
                                <ul class="max-h-48 overflow-y-auto py-1">
                                    <template x-if="filtered.length === 0">
                                        <li class="px-3 py-2 text-sm opacity-50">{{ __('No items found') }}</li>
                                    </template>
                                    <template x-for="item in filtered" :key="item.id">
                                        <li
                                            @click="select(item)"
                                            class="px-3 py-2 cursor-pointer hover:bg-base-200 text-sm"
                                            x-text="item.name"
                                        ></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        @error('item_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">{{ __('Quantity (negative for waste)') }}</span>
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
                            <span class="label-text">{{ __('Date') }}</span>
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
                            <span class="label-text">{{ __('Note') }}</span>
                        </label>
                        <textarea 
                            wire:model="note" 
                            class="textarea textarea-bordered w-full"
                            rows="3"
                        ></textarea>
                        @error('note') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="modal-action">
                        <button type="button" wire:click="closeModal" class="btn">
                            <x-icon.x />
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <x-icon.check />
                            {{ __('Save') }}
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
        :title="__('Delete Stock Opname')"
        :message="__('Are you sure you want to delete this stock opname? This action cannot be undone.')"
        :confirmText="__('Delete')"
        :cancelText="__('Cancel')"
        isDangerous="true"
        onConfirm="$wire.delete({{ $deleteId ?? 'null' }})"
    />
</div>
