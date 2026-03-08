<?php

namespace App\Livewire\StockInputs;

use App\Models\RawMaterial;
use App\Models\RawMaterialStockInput;
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
        $inputs = RawMaterialStockInput::with('rawMaterial')
            ->whereHas('rawMaterial', function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        $rawMaterials = RawMaterial::all();

        return view('livewire.stock-inputs.index', [
            'inputs' => $inputs,
            'rawMaterials' => $rawMaterials,
        ])->layout('components.app-layout', ['title' => 'Stock Input']);
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
            'qty' => 'required|numeric|min:0.01',
            'date' => 'required|date',
        ]);

        if ($this->editingId) {
            $input = RawMaterialStockInput::find($this->editingId);
            $oldQty = $input->qty;
            
            $input->update([
                'raw_material_id' => $this->raw_material_id,
                'qty' => $this->qty,
                'date' => $this->date,
                'note' => $this->note,
                'updated_by' => auth()->id(),
            ]);

            // Update stock
            $material = RawMaterial::find($this->raw_material_id);
            $diff = $this->qty - $oldQty;
            $material->increment('stock', $diff);

            // Update stock movement
            RawMaterialStockMovement::where('reference_id', $this->editingId)
                ->where('reference_type', 'PURCHASE')
                ->update(['qty' => $this->qty]);
        } else {
            $input = RawMaterialStockInput::create([
                'raw_material_id' => $this->raw_material_id,
                'qty' => $this->qty,
                'date' => $this->date,
                'note' => $this->note,
                'created_by' => auth()->id(),
            ]);

            // Increase stock
            $material = RawMaterial::find($this->raw_material_id);
            $material->increment('stock', $this->qty);

            // Record stock movement
            RawMaterialStockMovement::create([
                'raw_material_id' => $this->raw_material_id,
                'type' => 'PURCHASE',
                'qty' => $this->qty,
                'reference_id' => $input->id,
                'reference_type' => 'PURCHASE',
                'created_by' => auth()->id(),
            ]);
        }

        $this->closeModal();
        $this->dispatch('notify', message: 'Stock input saved successfully');
    }

    public function edit($id)
    {
        $input = RawMaterialStockInput::find($id);
        $this->editingId = $id;
        $this->raw_material_id = $input->raw_material_id;
        $this->qty = $input->qty;
        $this->date = $input->date->format('Y-m-d');
        $this->note = $input->note;
        $this->showModal = true;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('confirm-dialog');
    }

    public function delete($id)
    {
        $input = RawMaterialStockInput::find($id);
        
        // Reduce stock
        $material = RawMaterial::find($input->raw_material_id);
        $material->decrement('stock', $input->qty);

        // Delete stock movement
        RawMaterialStockMovement::where('reference_id', $id)
            ->where('reference_type', 'PURCHASE')
            ->delete();

        $input->delete();
        $this->deleteId = null;
        $this->dispatch('notify', message: 'Stock input deleted successfully');
    }
}
