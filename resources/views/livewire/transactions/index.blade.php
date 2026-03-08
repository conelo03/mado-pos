<div>
    <div class="mb-6 flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
        <div class="flex gap-2 w-full lg:w-auto">
            <input 
                type="text" 
                wire:model.live="search" 
                placeholder="Search invoice..." 
                class="input input-bordered flex-1 lg:flex-none lg:w-64"
            >
            <select 
                wire:model.live="statusFilter"
                class="select select-bordered"
            >
                <option value="">All Status</option>
                <option value="PAID">PAID</option>
                <option value="REFUND">REFUND</option>
                <option value="VOID">VOID</option>
            </select>
        </div>
        <button 
            wire:click="openModal" 
            class="btn btn-primary w-full lg:w-auto"
        >
            <x-icon.plus />
            New Transaction
        </button>
    </div>

    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Invoice</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr class="hover:bg-base-300">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $sale->invoice_no }}</td>
                        <td>{{ $sale->created_at->format('d M Y H:i') }}</td>
                        <td>Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                        <td>
                            <div class="badge {{ $sale->status === 'PAID' ? 'badge-success' : ($sale->status === 'VOID' ? 'badge-error' : 'badge-warning') }}">
                                {{ $sale->status }}
                            </div>
                        </td>
                        <td>
                            <div class="flex gap-1">
                                <button 
                                    onclick="window.open('{{ route('transactions.print', $sale->id) }}', '_blank')" 
                                    class="btn btn-xs btn-light" title="Print"
                                >
                                    <x-icon.printer />
                                </button>
                                <button 
                                    wire:click="openViewModal({{ $sale->id }})" 
                                    class="btn btn-xs btn-info" title="View"
                                >
                                    <x-icon.eye />
                                </button>
                                {{-- <button 
                                    wire:click="edit({{ $sale->id }})" 
                                    class="btn btn-xs btn-warning" title="Edit"
                                >
                                    <x-icon.pencil />
                                </button> --}}
                                @if($sale->status === 'PAID')
                                    <button 
                                        wire:click="confirmRefund({{ $sale->id }})" 
                                        class="btn btn-xs btn-warning" title="Refund"
                                    >
                                        <x-icon.arrow-uturn-left />
                                    </button>
                                    <button 
                                        wire:click="confirmCancel({{ $sale->id }})" 
                                        class="btn btn-xs btn-error" title="Cancel"
                                    >
                                        <x-icon.x />
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No transactions found</td>
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
            <div class="modal-box w-11/12 max-w-4xl">
                <h3 class="font-bold text-lg mb-4">{{ $editingId ? 'Edit' : 'New' }} Transaction</h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Products List -->
                    <div class="lg:col-span-2">
                        <div class="mb-3">
                            <h4 class="font-semibold mb-2">Products</h4>
                            <input 
                                type="text" 
                                wire:model.live="productSearch" 
                                placeholder="Search products..." 
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
                                <div class="text-center text-sm opacity-50 py-4">No products found</div>
                            @endforelse
                        </div>

                        <h4 class="font-semibold mt-6 mb-3">Items</h4>
                        <div class="border rounded-lg overflow-hidden">
                            <table class="table table-sm w-full">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Subtotal</th>
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
                                            <td colspan="5" class="text-center">No items added</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="bg-base-200 rounded-lg p-4 h-fit">
                        <h4 class="font-semibold mb-4">Summary</h4>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span>Subtotal:</span>
                                <span>Rp {{ number_format(array_sum(array_column($items, 'subtotal')), 0, ',', '.') }}</span>
                            </div>

                            <div class="flex justify-between">
                                <label>Discount:</label>
                                <input 
                                    type="number" 
                                    step="1"
                                    wire:model.live.debounce.500ms="discount"
                                    class="input input-bordered input-sm w-24"
                                >
                            </div>

                            <div class="divider my-2"></div>

                            <div class="flex justify-between font-semibold">
                                <span>Total:</span>
                                <span>Rp {{ number_format($total_price, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex justify-between">
                                <label>Paid Amount:</label>
                                <input 
                                    type="number" 
                                    step="1"
                                    wire:model.live="paid_amount"
                                    {{-- @change="$wire.updatePaidAmount($event.target.value)" --}}
                                    class="input input-bordered input-sm w-24"
                                    placeholder="Auto"
                                >
                            </div>

                            <div class="divider my-2"></div>

                            <div class="flex justify-between font-semibold">
                                <span>Change:</span>
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
                                Cancel
                            </button>
                            <button 
                                type="button"
                                wire:click="save" 
                                class="btn btn-primary btn-sm"
                            >
                                <x-icon.check />
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Confirm Delete Dialog -->
    <x-confirm-dialog 
        title="Delete Transaction"
        message="Are you sure you want to delete this transaction? This action cannot be undone."
        confirmText="Delete"
        cancelText="Cancel"
        isDangerous="true"
        onConfirm="$wire.delete({{ $deleteId ?? 'null' }})"
    />

    <!-- Confirm Refund Dialog -->
    <x-confirm-refund-dialog 
        title="Refund Transaction"
        message="Are you sure you want to refund this transaction? This action cannot be undone."
        confirmText="Refund"
        cancelText="Cancel"
        onConfirm="$wire.refund({{ $refundId ?? 'null' }})"
    />

    <!-- Confirm Cancel Dialog -->
    <x-confirm-cancel-dialog 
        title="Cancel Transaction"
        message="Are you sure you want to cancel this transaction? Stock will be restored and status will be set to VOID."
        confirmText="Cancel"
        cancelText="Close"
        onConfirm="$wire.cancel({{ $cancelId ?? 'null' }})"
    />

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
                
                <div class="space-y-6">
                    <!-- Items -->
                    <div>
                        <h4 class="font-semibold mb-3">Items</h4>
                        <div class="border rounded-lg overflow-hidden">
                            <table class="table table-sm w-full">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Subtotal</th>
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
                                            <td colspan="4" class="text-center">No items</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="bg-base-200 rounded-lg p-4 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>Rp {{ number_format(array_sum(array_column($viewItems, 'subtotal')), 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span>Discount:</span>
                            <span>Rp {{ number_format($discount, 0, ',', '.') }}</span>
                        </div>

                        <div class="divider my-2"></div>

                        <div class="flex justify-between font-semibold">
                            <span>Total:</span>
                            <span>Rp {{ number_format($total_price, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span>Paid Amount:</span>
                            <span>Rp {{ number_format($paid_amount, 0, ',', '.') }}</span>
                        </div>

                        <div class="divider my-2"></div>

                        <div class="flex justify-between font-semibold">
                            <span>Change:</span>
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
                        Close
                    </button>
                    <button 
                        type="button"
                        onclick="window.open('{{ route('transactions.print', $viewingId) }}', '_blank')" 
                        class="btn btn-primary btn-sm"
                    >
                        <x-icon.printer />
                        Print
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
