<?php

namespace App\Livewire\StockManagement;

use App\Models\Item;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class StockInput extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingId = null;
    public $item_id = '';
    public $qty = '';
    public $date = '';
    public $note = '';
    public $deleteId = null;

    public function mount()
    {
        $this->date = date('Y-m-d');
    }

    public function render()
    {
        $movements = StockMovement::where('reference_type', 'PURCHASE')
            ->whereHas('item', function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        $items = Item::where('is_active', true)->get();

        return view('livewire.stock-management.stock-input', [
            'movements' => $movements,
            'items' => $items,
        ])->layout('components.app-layout', ['title' => 'Stock Input']);
    }

    public function openModal()
    {
        $this->reset(['editingId', 'item_id', 'qty', 'note']);
        $this->date = date('Y-m-d');
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate([
            'item_id' => 'required|exists:items,id',
            'qty' => 'required|numeric|min:0.01',
            'date' => 'required|date',
        ]);

        if ($this->editingId) {
            $movement = StockMovement::find($this->editingId);
            $oldQty = $movement->qty;
            
            $movement->update([
                'item_id' => $this->item_id,
                'qty' => $this->qty,
                'date' => $this->date,
                'note' => $this->note,
                'updated_by' => auth()->id(),
            ]);

            // Update stock
            $item = Item::find($this->item_id);
            $diff = $this->qty - $oldQty;
            $item->increment('stock', $diff);
        } else {
            $movement = StockMovement::create([
                'item_id' => $this->item_id,
                'type' => 'IN',
                'qty' => $this->qty,
                'reference_type' => 'PURCHASE',
                'date' => $this->date,
                'note' => $this->note,
                'created_by' => auth()->id(),
            ]);

            // Increase stock
            $item = Item::find($this->item_id);
            $item->increment('stock', $this->qty);
        }

        $this->closeModal();
        $this->dispatch('notify', message: 'Stock input saved successfully');
    }

    public function edit($id)
    {
        $movement = StockMovement::find($id);
        $this->editingId = $id;
        $this->item_id = $movement->item_id;
        $this->qty = $movement->qty;
        $this->date = $movement->date->format('Y-m-d');
        $this->note = $movement->note;
        $this->showModal = true;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('confirm-dialog');
    }

    public function delete($id)
    {
        $movement = StockMovement::find($id);
        
        // Reduce stock
        $item = Item::find($movement->item_id);
        $item->decrement('stock', $movement->qty);

        $movement->delete();
        $this->deleteId = null;
        $this->dispatch('notify', message: 'Stock input deleted successfully');
    }
}
