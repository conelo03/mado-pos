<?php

namespace App\Livewire\Transactions;

use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\RawMaterialStockMovement;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

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

        $products = Product::where('is_active', true)
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
        $product = Product::find($productId);
        
        $existingKey = array_search($productId, array_column($this->items, 'product_id'));
        
        if ($existingKey !== false) {
            $this->items[$existingKey]['qty'] += 1;
            $this->items[$existingKey]['subtotal'] = $this->items[$existingKey]['price'] * $this->items[$existingKey]['qty'];
        } else {
            $this->items[] = [
                'product_id' => $productId,
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
        
        $this->total_price = $subtotal - $discount;
        
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
        $paidAmount = $this->paid_amount ?? $this->total_price;

        if ($this->editingId) {
            // Update existing transaction
            $sale = Sale::find($this->editingId);
            
            // Restore old stock first
            foreach ($sale->items as $item) {
                $this->restoreRawMaterialStock($item->product_id, $item->qty, $sale->id);
            }

            // Delete old stock movements
            RawMaterialStockMovement::where('reference_id', $sale->id)
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
                    'product_id' => $item['product_id'],
                    'price' => $item['price'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Reduce raw material stock
                $this->reduceRawMaterialStock($item['product_id'], $item['qty'], $sale->id);
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
                    'product_id' => $item['product_id'],
                    'price' => $item['price'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Reduce raw material stock
                $this->reduceRawMaterialStock($item['product_id'], $item['qty'], $sale->id);
            }

            $this->closeModal();
            $this->dispatch('notify', message: 'Transaction saved successfully');
        }
    }

    private function reduceRawMaterialStock($productId, $qty, $saleId)
    {
        $product = Product::find($productId);
        
        foreach ($product->boms as $bom) {
            $requiredQty = $bom->qty * $qty;
            
            $bom->rawMaterial->decrement('stock', $requiredQty);

            RawMaterialStockMovement::create([
                'raw_material_id' => $bom->raw_material_id,
                'type' => 'SALE',
                'qty' => $requiredQty,
                'reference_id' => $saleId,
                'reference_type' => 'SALE',
                'created_by' => auth()->id(),
            ]);
        }
    }

    public function edit($id)
    {
        $sale = Sale::with('items')->find($id);
        $this->editingId = $id;
        
        $this->items = $sale->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
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
        $sale = Sale::find($id);
        
        // Restore raw material stock
        foreach ($sale->items as $item) {
            $this->restoreRawMaterialStock($item->product_id, $item->qty, $sale->id);
        }

        // Delete stock movements
        RawMaterialStockMovement::where('reference_id', $sale->id)
            ->where('reference_type', 'SALE')
            ->delete();

        $sale->update(['status' => 'VOID']);
        $sale->delete();

        $this->deleteId = null;
        $this->dispatch('notify', message: 'Transaction deleted successfully');
    }

    private function restoreRawMaterialStock($productId, $qty, $saleId)
    {
        $product = Product::find($productId);
        
        foreach ($product->boms as $bom) {
            $requiredQty = $bom->qty * $qty;
            $bom->rawMaterial->increment('stock', $requiredQty);
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
