<?php

namespace App\Livewire\Transactions;

use App\Models\Sale;
use App\Models\Item;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingId = null;
    public $items = [];
    public $discount = null;
    public $paid_amount = null;
    public $total_price = 0;
    public $change_amount = 0;
    public $productSearch = '';
    public $deleteId = null;
    public $refundId = null;

    public function render()
    {
        $sales = Sale::where('invoice_no', 'like', "%{$this->search}%")
            ->latest()
            ->paginate(10);

        $products = Item::where('type', 'PRODUCT')
            ->where('is_active', true)
            ->where('name', 'like', "%{$this->productSearch}%")
            ->get();

        return view('livewire.transactions.index', [
            'sales' => $sales,
            'products' => $products,
        ])->layout('components.app-layout', ['title' => 'Transactions']);
    }

    public function openModal()
    {
        $this->reset(['editingId', 'items', 'discount', 'paid_amount', 'total_price', 'change_amount', 'productSearch']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function addItem($productId)
    {
        $product = Item::find($productId);
        
        $existingKey = array_search($productId, array_column($this->items, 'item_id'));
        
        if ($existingKey !== false) {
            $this->items[$existingKey]['qty'] += 1;
            $this->items[$existingKey]['subtotal'] = $this->items[$existingKey]['price'] * $this->items[$existingKey]['qty'];
        } else {
            $this->items[] = [
                'item_id' => $productId,
                'product_name' => $product->name,
                'price' => $product->price,
                'qty' => 1,
                'subtotal' => $product->price,
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
        }
        $this->calculateTotal();
    }

    public function incrementQty($index)
    {
        $this->items[$index]['qty'] += 1;
        $this->items[$index]['subtotal'] = $this->items[$index]['price'] * $this->items[$index]['qty'];
        $this->calculateTotal();
    }

    public function decrementQty($index)
    {
        if ($this->items[$index]['qty'] > 1) {
            $this->items[$index]['qty'] -= 1;
            $this->items[$index]['subtotal'] = $this->items[$index]['price'] * $this->items[$index]['qty'];
            $this->calculateTotal();
        }
    }

    public function calculateTotal()
    {
        $subtotal = array_sum(array_column($this->items, 'subtotal'));
        $discount = $this->discount ?? 0;
        $paid_amount = $this->paid_amount ?? 0;

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

    public function save()
    {
        if (empty($this->items)) {
            $this->dispatch('notify', message: 'Please add items to transaction');
            return;
        }

        $discount = $this->discount ?? 0;
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

                // Update sale
                $sale->update([
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
                        'qty' => $item['qty'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    // Reduce item stock
                    $this->reduceItemStock($item['item_id'], $item['qty'], $sale->id);
                }

                $this->closeModal();
                $this->dispatch('notify', message: 'Transaction updated successfully');
            } else {
                // Create new transaction
                $sale = Sale::create([
                    'invoice_no' => 'INV-' . date('YmdHis'),
                    'total_price' => $this->total_price,
                    'paid_amount' => $paidAmount,
                    'change_amount' => $paidAmount - $this->total_price,
                    'discount' => $discount,
                    'status' => 'PAID',
                    'created_by' => auth()->id(),
                ]);

                foreach ($this->items as $item) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'item_id' => $item['item_id'],
                        'price' => $item['price'],
                        'qty' => $item['qty'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    // Reduce item stock
                    $this->reduceItemStock($item['item_id'], $item['qty'], $sale->id);
                }

                $this->closeModal();
                $this->dispatch('notify', message: 'Transaction saved successfully');
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
        
        $this->items = $sale->items->map(function ($item) {
            return [
                'item_id' => $item->item_id,
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
        $this->dispatch('confirm-refund-dialog');
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
        $this->dispatch('notify', message: 'Transaction deleted successfully');
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
        $sale = Sale::find($id);
        $sale->update(['status' => 'REFUND']);
        $this->refundId = null;
        $this->dispatch('notify', message: 'Transaction refunded successfully');
    }
}
