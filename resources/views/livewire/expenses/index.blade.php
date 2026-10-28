<div>
    <div class="mb-6 flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-end">
        <div class="flex flex-col lg:flex-row gap-2 w-full lg:w-auto lg:items-end">
            <div>
                <label class="label">
                    <span class="label-text">{{ __('From Date') }}</span>
                </label>
                <input
                    type="date"
                    wire:model.live="startDate"
                    class="input input-bordered w-full"
                >
            </div>
            <div>
                <label class="label">
                    <span class="label-text">{{ __('To Date') }}</span>
                </label>
                <input
                    type="date"
                    wire:model.live="endDate"
                    class="input input-bordered w-full"
                >
            </div>
            <select
                wire:model.live="categoryFilter"
                class="select select-bordered"
            >
                <option value="">{{ __('All Categories') }}</option>
                @foreach($categories as $categoryOption)
                    <option value="{{ $categoryOption }}">{{ $categoryOption }}</option>
                @endforeach
            </select>
            <input
                type="text"
                wire:model.live="search"
                placeholder="{{ __('Search description...') }}"
                class="input input-bordered lg:w-56"
            >
        </div>
        <button
            wire:click="openModal"
            class="btn btn-primary w-full lg:w-auto"
        >
            <x-icon.plus />
            {{ __('Add Expense') }}
        </button>
    </div>

    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Category') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Created By') }}</th>
                    <th style="width: 100px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr class="hover:bg-base-300">
                        <td>{{ $expenses->firstItem() + $loop->index }}</td>
                        <td>{{ $expense->date->translatedFormat('d M Y') }}</td>
                        <td>
                            <div class="badge badge-outline">{{ $expense->category }}</div>
                        </td>
                        <td>{{ $expense->description ?? '-' }}</td>
                        <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                        <td>{{ $expense->createdBy?->name ?? '-' }}</td>
                        <td>
                            <div class="flex gap-1">
                                <button
                                    wire:click="edit({{ $expense->id }})"
                                    class="btn btn-xs btn-warning" title="{{ __('Edit') }}"
                                >
                                    <x-icon.pencil />
                                </button>
                                <button
                                    wire:click="confirmDelete({{ $expense->id }})"
                                    class="btn btn-xs btn-error" title="{{ __('Delete') }}"
                                >
                                    <x-icon.trash />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">{{ __('No expenses found') }}</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="font-bold bg-base-200">
                    <td colspan="4">{{ __('Total') }}</td>
                    <td colspan="3">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-4">
        {{ $expenses->links('vendor.pagination.tailwind') }}
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">{{ $editingId ? __('Edit Expense') : __('Add Expense') }}</h3>

                <form wire:submit="save">
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
                            <span class="label-text">{{ __('Category') }}</span>
                        </label>
                        <input
                            type="text"
                            wire:model="category"
                            list="expense-categories"
                            class="input input-bordered w-full"
                            placeholder="{{ __('Choose or type a category') }}"
                        >
                        <datalist id="expense-categories">
                            @foreach($categories as $categoryOption)
                                <option value="{{ $categoryOption }}"></option>
                            @endforeach
                        </datalist>
                        @error('category') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">{{ __('Amount') }}</span>
                        </label>
                        <x-currency-input wire-model="amount" />
                        @error('amount') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full mb-6">
                        <label class="label">
                            <span class="label-text">{{ __('Description') }}</span>
                        </label>
                        <input
                            type="text"
                            wire:model="description"
                            class="input input-bordered w-full"
                            placeholder="{{ __('Optional') }}"
                        >
                        @error('description') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="modal-action">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="btn"
                        >
                            <x-icon.x />
                            {{ __('Cancel') }}
                        </button>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            wire:loading.attr="disabled"
                            wire:target="save"
                        >
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
        :title="__('Delete Expense')"
        :message="__('Are you sure you want to delete this expense? This action cannot be undone.')"
        :confirmText="__('Delete')"
        :cancelText="__('Cancel')"
        isDangerous="true"
        onConfirm="$wire.delete({{ $deleteId ?? 'null' }})"
    />
</div>
