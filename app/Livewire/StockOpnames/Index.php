<?php

namespace App\Livewire\StockOpnames;

use App\Models\RawMaterial;
use App\Models\RawMaterialStockOpname;
use App\Models\RawMaterialStockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingId = null;
    public $raw_material_id = '';
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
        $opnames = RawMaterialStockOpname::with('rawMaterial')
            ->whereHas('rawMaterial', function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        $rawMaterials = RawMaterial::all();

        return view('livewire.stock-opnames.index', [
            'opnames' => $opnames,
            'rawMaterials' => $rawMaterials,
        ])->layout('components.app-layout', ['title' => 'Stock Opname']);
    }

    public function openModal()
    {
        $this->reset(['editingId', 'raw_material_id', 'qty', 'note']);
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
            'raw_material_id' => 'required|exists:raw_materials,id',
            'qty' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $material = RawMaterial::find($this->raw_material_id);
        $currentStock = $material->stock;

        if ($this->editingId) {
            $opname = RawMaterialStockOpname::find($this->editingId);
            $oldQty = $opname->qty;
            
            $opname->update([
                'raw_material_id' => $this->raw_material_id,
                'qty' => $this->qty,
                'date' => $this->date,
                'note' => $this->note,
                'updated_by' => auth()->id(),
            ]);

            // Adjust stock
            $diff = $this->qty - $oldQty;
            $material->increment('stock', $diff);

            // Update stock movement
            $type = $this->qty > 0 ? 'ADJUSTMENT' : 'WASTE';
            RawMaterialStockMovement::where('reference_id', $this->editingId)
                ->where('reference_type', 'ADJUSTMENT')
                ->orWhere('reference_type', 'WASTE')
                ->update(['qty' => abs($this->qty), 'type' => $type]);
        } else {
            $opname = RawMaterialStockOpname::create([
                'raw_material_id' => $this->raw_material_id,
                'qty' => $this->qty,
                'date' => $this->date,
                'note' => $this->note,
                'created_by' => auth()->id(),
            ]);

            // Adjust stock
            $material->increment('stock', $this->qty);

            // Record stock movement
            $type = $this->qty > 0 ? 'ADJUSTMENT' : 'WASTE';
            RawMaterialStockMovement::create([
                'raw_material_id' => $this->raw_material_id,
                'type' => $type,
                'qty' => abs($this->qty),
                'reference_id' => $opname->id,
                'reference_type' => $type,
                'created_by' => auth()->id(),
            ]);
        }

        $this->closeModal();
        $this->dispatch('notify', message: 'Stock opname saved successfully');
    }

    public function edit($id)
    {
        $opname = RawMaterialStockOpname::find($id);
        $this->editingId = $id;
        $this->raw_material_id = $opname->raw_material_id;
        $this->qty = $opname->qty;
        $this->date = $opname->date->format('Y-m-d');
        $this->note = $opname->note;
        $this->showModal = true;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('confirm-dialog');
    }

    public function delete($id)
    {
        $opname = RawMaterialStockOpname::find($id);
        
        // Reverse stock adjustment
        $material = RawMaterial::find($opname->raw_material_id);
        $material->decrement('stock', $opname->qty);

        // Delete stock movement
        RawMaterialStockMovement::where('reference_id', $id)
            ->where(function ($query) {
                $query->where('reference_type', 'ADJUSTMENT')
                    ->orWhere('reference_type', 'WASTE');
            })
            ->delete();

        $opname->delete();
        $this->deleteId = null;
        $this->dispatch('notify', message: 'Stock opname deleted successfully');
    }
}
