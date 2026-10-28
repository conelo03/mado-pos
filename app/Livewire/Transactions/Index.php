<?php

namespace App\Livewire\Transactions;

use App\Models\Sale;
use App\Models\Item;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\Customer;
use App\Models\PriceListType;
use App\Models\ItemPriceList;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $showModal = false;
    public $showViewModal = false;
    public $editingId = null;
    public $viewingId = null;
    public $customer_id = null;
    public $price_list_type_id = null;
    public $items = [];
    public $viewItems = [];
    public $viewInvoiceNo = '';
    public $viewDate = '';
    public $viewStatus = '';
    public $viewStatusBy = '';
    public $viewStatusAt = '';
    public $viewStatusReason = '';
    public $discount = null;
    public $paid_amount = null;
    public $total_price = 0;
    public $change_amount = 0;
    public $productSearch = '';
    public $deleteId = null;
    public $refundId = null;
    public $cancelId = null;
    public $statusReason = '';

    protected $listeners = ['setCustomer'];

    public function mount()
    {
        $this->statusFilter = 'PAID';
        $this->price_list_type_id = PriceListType::where('type', 'RETAIL')->first()?->id;
    }

    public function render()
    {
        $query = Sale::where('invoice_no', 'like', "%{$this->search}%");
        
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $sales = $query->latest()->paginate(10);

        $products = Item::where('type', 'PRODUCT')
            ->where('is_active', true)
            ->where('name', 'like', "%{$this->productSearch}%")
            ->get();

        $customers = Customer::all();
        $priceListTypes = PriceListType::all();

        return view('livewire.transactions.index', [
            'sales' => $sales,
            'products' => $products,
            'customers' => $customers,
            'priceListTypes' => $priceListTypes,
        ])->layout('components.app-layout', ['title' => __('Transactions')]);
    }

    public function openModal()
    {
        $this->reset(['editingId', 'items', 'discount', 'paid_amount', 'total_price', 'change_amount', 'productSearch', 'customer_id']);
        $this->price_list_type_id = PriceListType::where('type', 'RETAIL')->first()?->id;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function openViewModal($id)
    {
        $sale = Sale::with('items', 'refundedBy', 'cancelledBy')->find($id);
        $this->viewingId = $id;
        $this->viewInvoiceNo = $sale->invoice_no;
        $this->viewDate = $sale->created_at->translatedFormat('d M Y H:i');

        $isRefund = $sale->status === 'REFUND';
        $this->viewStatus = $sale->status;
        $this->viewStatusBy = ($isRefund ? $sale->refundedBy : $sale->cancelledBy)?->name ?? '-';
        $this->viewStatusAt = ($isRefund ? $sale->refunded_at : $sale->cancelled_at)?->translatedFormat('d M Y H:i') ?? '-';
        $this->viewStatusReason = $sale->status_reason ?? '';

        $this->viewItems = $sale->items->map(function ($item) {
            return [
                'product_name' => $item->item->name,
                'price' => $item->price,
                'qty' => $item->qty,
                'subtotal' => $item->subtotal,
            ];
        })->toArray();

        $this->discount = $sale->discount;
        $this->paid_amount = $sale->paid_amount;
        $this->total_price = $sale->total_price;
        $this->change_amount = $sale->change_amount;
        
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
    }

    public function getItemPrice($itemId)
    {
        // If price_list_type is selected, try to get price from item_price_lists
        if ($this->price_list_type_id) {
            $itemPrice = ItemPriceList::where('item_id', $itemId)
                ->where('price_list_type_id', $this->price_list_type_id)
                ->first();
            
            if ($itemPrice) {
                return $itemPrice->price;
            }
        }

        // Fallback to item's default price
        $item = Item::find($itemId);
        return $item->price;
    }

    public function addItem($productId)
    {
        $product = Item::find($productId);
        $price = $this->getItemPrice($productId);
        
        $existingKey = array_search($productId, array_column($this->items, 'item_id'));
        
        if ($existingKey !== false) {
            $this->items[$existingKey]['qty'] += 1;
            $this->items[$existingKey]['subtotal'] = $this->items[$existingKey]['price'] * $this->items[$existingKey]['qty'];
            $this->items[$existingKey]['cost_subtotal'] = $this->items[$existingKey]['cost'] * $this->items[$existingKey]['qty'];
        } else {
            $this->items[] = [
                'item_id' => $productId,
                'product_name' => $product->name,
                'price' => $price,
                'cost' => $product->cost,
                'qty' => 1,
                'subtotal' => $price,
                'cost_subtotal' => $product->cost,
            ];
        }

        $this->calculateTotal();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotal();
    }

    public function updateQty($index, $qty)
    {
        if ($qty > 0) {
            $this->items[$index]['qty'] = $qty;
            $this->items[$index]['subtotal'] = $this->items[$index]['price'] * $qty;
            $this->items[$index]['cost_subtotal'] = $this->items[$index]['cost'] * $qty;
        }
        $this->calculateTotal();
    }

    public function updateCost($index, $cost)
    {
        $this->items[$index]['cost'] = $cost;
        $this->items[$index]['cost_subtotal'] = $cost * $this->items[$index]['qty'];
        $this->calculateTotal();
    }

    public function incrementQty($index)
    {
        $this->items[$index]['qty'] += 1;
        $this->items[$index]['subtotal'] = $this->items[$index]['price'] * $this->items[$index]['qty'];
        $this->items[$index]['cost_subtotal'] = $this->items[$index]['cost'] * $this->items[$index]['qty'];
        $this->calculateTotal();
    }

    public function decrementQty($index)
    {
        if ($this->items[$index]['qty'] > 1) {
            $this->items[$index]['qty'] -= 1;
            $this->items[$index]['subtotal'] = $this->items[$index]['price'] * $this->items[$index]['qty'];
            $this->items[$index]['cost_subtotal'] = $this->items[$index]['cost'] * $this->items[$index]['qty'];
            $this->calculateTotal();
        }
    }

    public function calculateTotal()
    {
        $subtotal = array_sum(array_column($this->items, 'subtotal'));
        $discount = (float) $this->discount ?? 0;
        $paid_amount = (float) $this->paid_amount ?? 0;

        if ($discount > 0) {
            $this->total_price = $subtotal - $discount;
        } else {
            $this->total_price = $subtotal;
        }
        
        if ($paid_amount > 0) {
            $this->change_amount = $paid_amount - $this->total_price;
        } else {
            $this->change_amount = 0;
        }
    }

    public function updatedDiscount()
    {
        $this->calculateTotal();
    }

    public function updatedPaidAmount()
    {
        $this->calculateTotal();
    }

    public function setCustomer($value)
    {
        $this->customer_id = $value;
        $this->updatedCustomerId();
    }

    public function updatedCustomerId()
    {
        // When customer is selected, update price_list_type_id to customer's price list type
        if ($this->customer_id) {
            $customer = Customer::find($this->customer_id);
            if ($customer && $customer->price_list_type_id) {
                $this->price_list_type_id = $customer->price_list_type_id;
            }
        } else {
            // If no customer selected, default to RETAIL
            $this->price_list_type_id = PriceListType::where('type', 'RETAIL')->first()?->id;
        }
        
        // Recalculate prices for all items in cart
        $this->recalculateItemPrices();
    }

    public function updatedPriceListTypeId()
    {
        // When price type changes, recalculate prices for all items in cart
        $this->recalculateItemPrices();
    }

    private function recalculateItemPrices()
    {
        foreach ($this->items as &$item) {
            $newPrice = $this->getItemPrice($item['item_id']);
            $item['price'] = $newPrice;
            $item['subtotal'] = $newPrice * $item['qty'];
        }
        $this->calculateTotal();
    }

    public function save()
    {
        if (empty($this->items)) {
            $this->dispatch('notify', message: __('Please add items to transaction'));
            return;
        }

        $discount = (float) $this->discount ?? 0;
        $paidAmount = $this->total_price;

        if ($this->paid_amount > 0) {
            $paidAmount = $this->paid_amount;
        }

        DB::transaction(function () use ($discount, $paidAmount) {
            if ($this->editingId) {
                // Update existing transaction
                $sale = Sale::find($this->editingId);
                
                // Restore old stock first
                foreach ($sale->items as $item) {
                    $this->restoreItemStock($item->item_id, $item->qty, $sale->id);
                }

                // Delete old stock movements
                StockMovement::where('reference_id', $sale->id)
                    ->where('reference_type', 'SALE')
                    ->delete();

                // Delete old items
                SaleItem::where('sale_id', $sale->id)->delete();

                // Calculate total cost
                $totalCost = 0;

                // Update sale
                $sale->update([
                    'customer_id' => $this->customer_id,
                    'price_list_type_id' => $this->price_list_type_id,
                    'total_cost' => $totalCost,
                    'total_price' => $this->total_price,
                    'paid_amount' => $paidAmount,
                    'change_amount' => $paidAmount - $this->total_price,
                    'discount' => $discount,
                    'updated_by' => auth()->id(),
                ]);

                // Add new items
                foreach ($this->items as $item) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'item_id' => $item['item_id'],
                        'price' => $item['price'],
                        'cost' => $item['cost'],
                        'qty' => $item['qty'],
                        'subtotal' => $item['subtotal'],
                        'cost_subtotal' => $item['cost_subtotal'],
                    ]);

                    $totalCost += $item['cost_subtotal'];

                    // Reduce item stock
                    $this->reduceItemStock($item['item_id'], $item['qty'], $sale->id);
                }

                // Update total cost after adding items
                $sale->update(['total_cost' => $totalCost]);

                $this->closeModal();
                $this->dispatch('notify', message: __('Transaction updated successfully'));
            } else {
                // Create new transaction
                $totalCost = 0;

                foreach ($this->items as $item) {
                    $totalCost += $item['cost_subtotal'];
                }

                $sale = Sale::create([
                    'customer_id' => $this->customer_id,
                    'price_list_type_id' => $this->price_list_type_id,
                    'invoice_no' => (string) Str::uuid(),
                    'total_cost' => $totalCost,
                    'total_price' => $this->total_price,
                    'paid_amount' => $paidAmount,
                    'change_amount' => $paidAmount - $this->total_price,
                    'discount' => $discount,
                    'status' => 'PAID',
                    'created_by' => auth()->id(),
                ]);

                $sale->update([
                    'invoice_no' => 'INV-' . $sale->created_at->format('Ymd') . '-' . str_pad($sale->id, 6, '0', STR_PAD_LEFT),
                ]);

                foreach ($this->items as $item) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'item_id' => $item['item_id'],
                        'price' => $item['price'],
                        'cost' => $item['cost'],
                        'qty' => $item['qty'],
                        'subtotal' => $item['subtotal'],
                        'cost_subtotal' => $item['cost_subtotal'],
                    ]);

                    // Reduce item stock
                    $this->reduceItemStock($item['item_id'], $item['qty'], $sale->id);
                }

                $this->closeModal();
                $this->dispatch('notify', message: __('Transaction saved successfully'));
            }
        });
    }

    private function reduceItemStock($itemId, $qty, $saleId)
    {
        $item = Item::find($itemId);
        
        // If is_track_stock is true, reduce item stock directly
        if ($item->is_track_stock) {
            $item->decrement('stock', $qty);
            
            StockMovement::create([
                'item_id' => $itemId,
                'type' => 'OUT',
                'qty' => $qty,
                'reference_id' => $saleId,
                'reference_type' => 'SALE',
                'date' => now()->toDateString(),
                'created_by' => auth()->id(),
            ]);
        } else {
            // If is_track_stock is false, check item_boms to reduce materials
            foreach ($item->boms as $bom) {
                $requiredQty = $bom->qty * $qty;
                
                $bom->material->decrement('stock', $requiredQty);

                StockMovement::create([
                    'item_id' => $bom->material_id,
                    'type' => 'OUT',
                    'qty' => $requiredQty,
                    'reference_id' => $saleId,
                    'reference_type' => 'SALE',
                    'date' => now()->toDateString(),
                    'created_by' => auth()->id(),
                ]);
            }
        }
    }

    public function edit($id)
    {
        $sale = Sale::with('items')->find($id);
        $this->editingId = $id;
        $this->customer_id = $sale->customer_id;
        $this->price_list_type_id = $sale->price_list_type_id;
        
        $this->items = $sale->items->map(function ($item) {
            return [
                'item_id' => $item->item_id,
                'product_name' => $item->item->name,
                'price' => $item->price,
                'cost' => $item->cost,
                'qty' => $item->qty,
                'subtotal' => $item->subtotal,
                'cost_subtotal' => $item->cost_subtotal,
            ];
        })->toArray();

        $this->discount = $sale->discount;
        $this->paid_amount = $sale->paid_amount;
        $this->total_price = $sale->total_price;
        $this->change_amount = $sale->change_amount;
        
        $this->showModal = true;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('confirm-dialog');
    }

    public function confirmRefund($id)
    {
        $this->refundId = $id;
        $this->statusReason = '';
        $this->dispatch('confirm-refund-dialog');
    }

    public function confirmCancel($id)
    {
        $this->cancelId = $id;
        $this->statusReason = '';
        $this->dispatch('confirm-cancel-dialog');
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $sale = Sale::find($id);
            
            // Restore item stock
            foreach ($sale->items as $item) {
                $this->restoreItemStock($item->item_id, $item->qty, $sale->id);
            }

            // Delete stock movements
            StockMovement::where('reference_id', $sale->id)
                ->where('reference_type', 'SALE')
                ->delete();

            $sale->update(['status' => 'VOID']);
            $sale->delete();
        });

        $this->deleteId = null;
        $this->dispatch('notify', message: __('Transaction deleted successfully'));
    }

    private function restoreItemStock($itemId, $qty, $saleId)
    {
        $item = Item::find($itemId);
        
        // If is_track_stock is true, restore item stock directly
        if ($item->is_track_stock) {
            $item->increment('stock', $qty);
        } else {
            // If is_track_stock is false, check item_boms to restore materials
            foreach ($item->boms as $bom) {
                $requiredQty = $bom->qty * $qty;
                $bom->material->increment('stock', $requiredQty);
            }
        }
    }

    public function refund($id)
    {
        $this->authorizeStatusChange();

        $refunded = DB::transaction(function () use ($id) {
            $sale = Sale::lockForUpdate()->find($id);

            if ($sale?->status !== 'PAID') {
                return false;
            }

            $sale->update([
                'status' => 'REFUND',
                'refunded_at' => now(),
                'refunded_by' => auth()->id(),
                'status_reason' => trim((string) $this->statusReason) ?: null,
                'updated_by' => auth()->id(),
            ]);

            return true;
        });

        $this->reset(['refundId', 'statusReason']);
        $this->dispatch('notify', message: $refunded
            ? __('Transaction refunded successfully')
            : __('Transaction is no longer PAID'));
    }

    public function cancel($id)
    {
        $this->authorizeStatusChange();

        $cancelled = DB::transaction(function () use ($id) {
            $sale = Sale::lockForUpdate()->find($id);

            if ($sale?->status !== 'PAID') {
                return false;
            }

            $saleMovements = StockMovement::where('reference_id', $sale->id)
                ->where('reference_type', 'SALE')
                ->get();

            foreach ($saleMovements as $movement) {
                Item::withTrashed()->whereKey($movement->item_id)->increment('stock', $movement->qty);

                StockMovement::create([
                    'item_id' => $movement->item_id,
                    'type' => 'IN',
                    'qty' => $movement->qty,
                    'reference_id' => $sale->id,
                    'reference_type' => 'SALE_CANCEL',
                    'date' => now()->toDateString(),
                    'created_by' => auth()->id(),
                ]);
            }

            $sale->update([
                'status' => 'VOID',
                'cancelled_at' => now(),
                'cancelled_by' => auth()->id(),
                'status_reason' => trim((string) $this->statusReason) ?: null,
                'updated_by' => auth()->id(),
            ]);

            return true;
        });

        $this->reset(['cancelId', 'statusReason']);
        $this->dispatch('notify', message: $cancelled
            ? __('Transaction cancelled successfully')
            : __('Transaction is no longer PAID'));
    }

    private function authorizeStatusChange()
    {
        $role = auth()->user()->role;

        if ($role !== 'ADMIN' && $role !== 'SUPERADMIN') {
            abort(403, 'Unauthorized access');
        }
    }
}
