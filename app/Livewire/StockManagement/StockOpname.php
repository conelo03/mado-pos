<?php

namespace App\Livewire\StockManagement;

use App\Models\Item;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class StockOpname extends Component
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
        $movements = StockMovement::whereIn('reference_type', ['ADJUSTMENT', 'WASTE'])
            ->whereHas('item', function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        $items = Item::where('is_active', true)->get();

        return view('livewire.stock-management.stock-opname', [
            'movements' => $movements,
            'items' => $items,
        ])->layout('components.app-layout', ['title' => 'Stock Opname']);
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
            'qty' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $item = Item::find($this->item_id);

        if ($this->editingId) {
            $movement = StockMovement::find($this->editingId);
            $oldQty = $movement->qty;
            
            $movement->update([
                'item_id' => $this->item_id,
                'qty' => abs($this->qty),
                'reference_type' => $this->qty > 0 ? 'ADJUSTMENT' : 'WASTE',
                'date' => $this->date,
                'note' => $this->note,
                'updated_by' => auth()->id(),
            ]);

            // Adjust stock
            $diff = $this->qty - $oldQty;
            $item->increment('stock', $diff);
        } else {
            $movement = StockMovement::create([
                'item_id' => $this->item_id,
                'type' => $this->qty > 0 ? 'IN' : 'OUT',
                'qty' => abs($this->qty),
                'reference_type' => $this->qty > 0 ? 'ADJUSTMENT' : 'WASTE',
                'date' => $this->date,
                'note' => $this->note,
                'created_by' => auth()->id(),
            ]);

            // Adjust stock
            $item->increment('stock', $this->qty);
        }

        $this->closeModal();
        $this->dispatch('notify', message: 'Stock opname saved successfully');
    }

    public function edit($id)
    {
        $movement = StockMovement::find($id);
        $this->editingId = $id;
        $this->item_id = $movement->item_id;
        $this->qty = $movement->reference_type === 'WASTE' ? -$movement->qty : $movement->qty;
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
        
        // Reverse stock adjustment
        $item = Item::find($movement->item_id);
        $qty = $movement->reference_type === 'WASTE' ? $movement->qty : -$movement->qty;
        $item->increment('stock', $qty);

        $movement->delete();
        $this->deleteId = null;
        $this->dispatch('notify', message: 'Stock opname deleted successfully');
    }
}
