<div>
    <div class="mb-6 flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
        <div class="flex gap-2 w-full lg:w-auto">
            <input 
                type="text" 
                wire:model.live="search" 
                placeholder="{{ __('Search invoice...') }}" 
                class="input input-bordered flex-1 lg:flex-none lg:w-64"
            >
            <select 
                wire:model.live="statusFilter"
                class="select select-bordered"
            >
                <option value="">{{ __('All Status') }}</option>
                <option value="PAID">{{ __('PAID') }}</option>
                <option value="REFUND">{{ __('REFUND') }}</option>
                <option value="VOID">{{ __('VOID') }}</option>
            </select>
        </div>
        <button 
            wire:click="openModal" 
            class="btn btn-primary w-full lg:w-auto"
        >
            <x-icon.plus />
            {{ __('New Transaction') }}
        </button>
    </div>

    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>{{ __('Invoice') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Total') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th style="width: 100px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr class="hover:bg-base-300">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $sale->invoice_no }}</td>
                        <td>{{ $sale->created_at->translatedFormat('d M Y H:i') }}</td>
                        <td>Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                        <td>
                            <div class="badge {{ $sale->status === 'PAID' ? 'badge-success' : ($sale->status === 'VOID' ? 'badge-error' : 'badge-warning') }}">
                                {{ __($sale->status) }}
                            </div>
                        </td>
                        <td>
                            <div class="flex gap-1">
                                <button 
                                    onclick="window.open('{{ route('transactions.print', $sale->id) }}', '_blank')" 
                                    class="btn btn-xs btn-light" title="{{ __('Print') }}"
                                >
                                    <x-icon.printer />
                                </button>
                                <button 
                                    wire:click="openViewModal({{ $sale->id }})" 
                                    class="btn btn-xs btn-info" title="{{ __('View') }}"
                                >
                                    <x-icon.eye />
                                </button>
                                {{-- <button 
                                    wire:click="edit({{ $sale->id }})" 
                                    class="btn btn-xs btn-warning" title="{{ __('Edit') }}"
                                >
                                    <x-icon.pencil />
                                </button> --}}
                                @if($sale->status === 'PAID' && (auth()->user()->role === 'ADMIN' || auth()->user()->role === 'SUPERADMIN'))
                                    <button
                                        wire:click="confirmRefund({{ $sale->id }})"
                                        class="btn btn-xs btn-warning" title="{{ __('Refund') }}"
                                    >
                                        <x-icon.arrow-uturn-left />
                                    </button>
                                    <button 
                                        wire:click="confirmCancel({{ $sale->id }})" 
                                        class="btn btn-xs btn-error" title="{{ __('Cancel Transaction') }}"
                                    >
                                        <x-icon.x />
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">{{ __('No transactions found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $sales->links('vendor.pagination.tailwind') }}
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal modal-open" onclick="event.target === this || event.stopPropagation()">
            <div class="modal-box w-11/12 max-w-4xl overflow-visible">
                <h3 class="font-bold text-lg mb-4">{{ $editingId ? __('Edit Transaction') : __('New Transaction') }}</h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Customer & Price Type Selection -->
                    <div class="lg:col-span-3 bg-base-200 rounded-lg p-4">
                        <h4 class="font-semibold mb-3">{{ __('Customer & Pricing') }}</h4>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">{{ __('Customer (Optional)') }}</span>
                                </label>
                                <div
                                    x-data="{
                                        open: false,
                                        search: '',
                                        selectedLabel: '',
                                        customers: {{ Js::from($customers->map(fn($c) => ['id' => $c->id, 'name' => $c->name . ($c->priceListType ? ' (' . $c->priceListType->type . ')' : '')])) }},
                                        get filtered() {
                                            if (!this.search) return this.customers;
                                            return this.customers.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()));
                                        },
                                        select(customer) {
                                            this.selectedLabel = customer ? customer.name : '';
                                            this.search = '';
                                            this.open = false;
                                            $wire.setCustomer(customer ? customer.id : null);
                                        },
                                        clear() {
                                            this.selectedLabel = '';
                                            this.search = '';
                                            this.open = false;
                                            $wire.setCustomer(null);
                                        }
                                    }"
                                    class="relative"
                                >
                                    <div class="flex gap-1">
                                        <button
                                            type="button"
                                            @click="open = !open"
                                            class="select select-bordered select-sm flex-1 text-left flex items-center justify-between h-9"
                                        >
                                            <span x-text="selectedLabel || {{ Js::from(__('Walk-in Customer')) }}" :class="selectedLabel ? '' : 'opacity-50'"></span>
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                        <button
                                            type="button"
                                            x-show="selectedLabel"
                                            @click="clear()"
                                            class="btn btn-square btn-sm h-9 w-9"
                                            title="{{ __('Clear') }}"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>

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
                                                placeholder="{{ __('Search customer...') }}"
                                                class="input input-sm input-bordered w-full"
                                                x-init="$watch('open', val => val && $nextTick(() => $refs.searchInput.focus()))"
                                            >
                                        </div>
                                        <ul class="max-h-48 overflow-y-auto py-1">
                                            <li
                                                @click="clear()"
                                                class="px-3 py-2 cursor-pointer hover:bg-base-200 text-sm opacity-60"
                                            >{{ __('Walk-in Customer') }}</li>
                                            <template x-if="filtered.length === 0">
                                                <li class="px-3 py-2 text-sm opacity-50">{{ __('No customers found') }}</li>
                                            </template>
                                            <template x-for="customer in filtered" :key="customer.id">
                                                <li
                                                    @click="select(customer)"
                                                    class="px-3 py-2 cursor-pointer hover:bg-base-200 text-sm"
                                                    x-text="customer.name"
                                                ></li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">{{ __('Price Type') }}{{ $customer_id ? ' (' . __('Auto') . ')' : '' }}</span>
                                </label>
                                @if($customer_id)
                                    <div class="select select-bordered select-sm bg-base-300 cursor-not-allowed w-100 h-9">
                                        {{ $priceListTypes->firstWhere('id', $price_list_type_id)?->name }} ({{ $priceListTypes->firstWhere('id', $price_list_type_id)?->type }})
                                    </div>
                                @else
                                    <select wire:model.live="price_list_type_id" class="select select-bordered select-sm w-100 h-9">
                                        @foreach($priceListTypes as $priceType)
                                            <option value="{{ $priceType->id }}">{{ $priceType->name }} ({{ $priceType->type }})</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Products List -->
                    <div class="lg:col-span-2">
                        <div class="mb-3">
                            <h4 class="font-semibold mb-2">{{ __('Products') }}</h4>
                            <input 
                                type="text" 
                                wire:model.live="productSearch" 
                                placeholder="{{ __('Search products...') }}" 
                                class="input input-bordered w-full input-sm"
                            >
                        </div>
                        <div class="space-y-2 min-h-64 max-h-64 overflow-y-auto border rounded-lg p-3">
                            @forelse($products as $product)
                                <button 
                                    wire:click="addItem({{ $product->id }})"
                                    class="w-full text-left px-3 py-2 hover:bg-base-200 border rounded"
                                >
                                    <div class="font-medium">{{ $product->name }}</div>
                                    <div class="text-sm opacity-75">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                                </button>
                            @empty
                                <div class="text-center text-sm opacity-50 py-4">{{ __('No products found') }}</div>
                            @endforelse
                        </div>

                        <h4 class="font-semibold mt-6 mb-3">{{ __('Order Items') }}</h4>
                        <div class="border rounded-lg overflow-hidden">
                            <table class="table table-sm w-full">
                                <thead>
                                    <tr>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Qty') }}</th>
                                        <th>{{ __('Subtotal') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($items as $index => $item)
                                        <tr>
                                            <td>{{ $item['product_name'] }}</td>
                                            <td>Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                                            <td>
                                                <div class="flex gap-1 items-center">
                                                    <button 
                                                        wire:click="decrementQty({{ $index }})"
                                                        class="btn btn-xs btn-outline"
                                                    >
                                                        <x-icon.minus />
                                                    </button>
                                                    <input 
                                                        type="number" 
                                                        wire:model="items.{{ $index }}.qty"
                                                        class="input input-bordered input-sm w-16 text-center"
                                                        readonly
                                                        min="1"
                                                    >
                                                    <button 
                                                        wire:click="incrementQty({{ $index }})"
                                                        class="btn btn-xs btn-outline"
                                                    >
                                                        <x-icon.plus />
                                                    </button>
                                                </div>
                                            </td>
                                            <td>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                            <td>
                                                <button 
                                                    wire:click="removeItem({{ $index }})"
                                                    class="btn btn-xs btn-error text-white"
                                                >
                                                    <x-icon.trash />
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">{{ __('No items added') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="bg-base-200 rounded-lg p-4 h-fit">
                        <h4 class="font-semibold mb-4">{{ __('Summary') }}</h4>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span>{{ __('Subtotal') }}:</span>
                                <span>Rp {{ number_format(array_sum(array_column($items, 'subtotal')), 0, ',', '.') }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <label>{{ __('Discount') }}:</label>
                                <div class="w-28">
                                    <x-currency-input
                                        wire-model="discount"
                                        size="sm"
                                        :live="true"
                                        live-method="updatedDiscount"
                                        :debounce="500"
                                        placeholder="0"
                                    />
                                </div>
                            </div>

                            <div class="divider my-2"></div>

                            <div class="flex justify-between font-semibold">
                                <span>{{ __('Total') }}:</span>
                                <span>Rp {{ number_format($total_price, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <label>{{ __('Paid Amount') }}:</label>
                                <div class="w-28">
                                    <x-currency-input
                                        wire-model="paid_amount"
                                        size="sm"
                                        :live="true"
                                        live-method="updatedPaidAmount"
                                        :placeholder="__('Auto')"
                                    />
                                </div>
                            </div>

                            <div class="divider my-2"></div>

                            <div class="flex justify-between font-semibold">
                                <span>{{ __('Change') }}:</span>
                                <span>Rp {{ number_format($change_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="modal-action mt-6">
                            <button 
                                type="button"
                                wire:click="closeModal" 
                                class="btn btn-sm"
                            >
                                <x-icon.x />
                                {{ __('Cancel') }}
                            </button>
                            <button 
                                type="button"
                                wire:click="save"
                                wire:loading.attr="disabled"
                                wire:target="save"
                                class="btn btn-primary btn-sm"
                            >
                                <x-icon.check />
                                {{ __('Save') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Confirm Delete Dialog -->
    <x-confirm-dialog 
        :title="__('Delete Transaction')"
        :message="__('Are you sure you want to delete this transaction? This action cannot be undone.')"
        :confirmText="__('Delete')"
        :cancelText="__('Cancel')"
        isDangerous="true"
        onConfirm="$wire.delete({{ $deleteId ?? 'null' }})"
    />

    <!-- Confirm Refund Dialog -->
    <x-confirm-refund-dialog 
        :title="__('Refund Transaction')"
        :message="__('Are you sure you want to refund this transaction? This action cannot be undone.')"
        :confirmText="__('Refund')"
        :cancelText="__('Cancel')"
        onConfirm="$wire.refund({{ $refundId ?? 'null' }})"
    >
        <textarea
            wire:model="statusReason"
            class="textarea textarea-bordered w-full"
            rows="2"
            placeholder="{{ __('Reason (optional)') }}"
        ></textarea>
    </x-confirm-refund-dialog>

    <!-- Confirm Cancel Dialog -->
    <x-confirm-cancel-dialog
        :title="__('Cancel Transaction')"
        :message="__('Are you sure you want to cancel this transaction? Stock will be restored and status will be set to VOID.')"
        :confirmText="__('Cancel Transaction')"
        :cancelText="__('Close')"
        onConfirm="$wire.cancel({{ $cancelId ?? 'null' }})"
    >
        <textarea
            wire:model="statusReason"
            class="textarea textarea-bordered w-full"
            rows="2"
            placeholder="{{ __('Reason (optional)') }}"
        ></textarea>
    </x-confirm-cancel-dialog>

    <!-- View Modal -->
    @if($showViewModal)
        <div class="modal modal-open" onclick="event.target === this || event.stopPropagation()">
            <div class="modal-box w-11/12 max-w-2xl">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-bold text-lg">{{ $viewInvoiceNo }}</h3>
                        <p class="text-sm text-base-content/60">{{ $viewDate }}</p>
                    </div>
                </div>

                @if(in_array($viewStatus, ['REFUND', 'VOID']))
                    <div class="bg-base-200 rounded-lg p-4 mb-6 text-sm space-y-1">
                        <div>
                            <span class="font-semibold">{{ $viewStatus === 'REFUND' ? __('Refunded by') : __('Cancelled by') }}:</span>
                            {{ $viewStatusBy }} · {{ $viewStatusAt }}
                        </div>
                        @if($viewStatusReason)
                            <div>
                                <span class="font-semibold">{{ __('Reason') }}:</span>
                                {{ $viewStatusReason }}
                            </div>
                        @endif
                    </div>
                @endif
                
                <div class="space-y-6">
                    <!-- Items -->
                    <div>
                        <h4 class="font-semibold mb-3">{{ __('Order Items') }}</h4>
                        <div class="border rounded-lg overflow-hidden">
                            <table class="table table-sm w-full">
                                <thead>
                                    <tr>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Qty') }}</th>
                                        <th>{{ __('Subtotal') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($viewItems as $item)
                                        <tr>
                                            <td>{{ $item['product_name'] }}</td>
                                            <td>Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                                            <td>{{ $item['qty'] }}</td>
                                            <td>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">{{ __('No items') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="bg-base-200 rounded-lg p-4 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span>{{ __('Subtotal') }}:</span>
                            <span>Rp {{ number_format(array_sum(array_column($viewItems, 'subtotal')), 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span>{{ __('Discount') }}:</span>
                            <span>Rp {{ number_format($discount, 0, ',', '.') }}</span>
                        </div>

                        <div class="divider my-2"></div>

                        <div class="flex justify-between font-semibold">
                            <span>{{ __('Total') }}:</span>
                            <span>Rp {{ number_format($total_price, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span>{{ __('Paid Amount') }}:</span>
                            <span>Rp {{ number_format($paid_amount, 0, ',', '.') }}</span>
                        </div>

                        <div class="divider my-2"></div>

                        <div class="flex justify-between font-semibold">
                            <span>{{ __('Change') }}:</span>
                            <span>Rp {{ number_format($change_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="modal-action mt-6">
                    <button 
                        type="button"
                        wire:click="closeViewModal" 
                        class="btn btn-sm"
                    >
                        <x-icon.x />
                        {{ __('Close') }}
                    </button>
                    <button 
                        type="button"
                        onclick="window.open('{{ route('transactions.print', $viewingId) }}', '_blank')" 
                        class="btn btn-primary btn-sm"
                    >
                        <x-icon.printer />
                        {{ __('Print') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
