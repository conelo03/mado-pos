<?php

namespace App\Livewire\RawMaterials;

use App\Models\RawMaterial;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingId = null;
    public $name = '';
    public $unit = '';
    public $minimum_stock = '';
    public $deleteId = null;

    public function render()
    {
        $rawMaterials = RawMaterial::where('name', 'like', "%{$this->search}%")
            ->paginate(10);

        return view('livewire.raw-materials.index', [
            'rawMaterials' => $rawMaterials,
        ])->layout('components.app-layout', ['title' => 'Raw Materials']);
    }

    public function openModal()
    {
        $this->reset(['editingId', 'name', 'unit', 'minimum_stock']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string',
            'unit' => 'required|string',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        if ($this->editingId) {
            $material = RawMaterial::find($this->editingId);
            $material->update([
                'name' => $this->name,
                'unit' => $this->unit,
                'minimum_stock' => $this->minimum_stock,
                'updated_by' => auth()->id(),
            ]);
        } else {
            RawMaterial::create([
                'name' => $this->name,
                'unit' => $this->unit,
                'stock' => 0,
                'minimum_stock' => $this->minimum_stock,
                'created_by' => auth()->id(),
            ]);
        }

        $this->closeModal();
        $this->dispatch('notify', message: 'Raw material saved successfully');
    }

    public function edit($id)
    {
        $material = RawMaterial::find($id);
        $this->editingId = $id;
        $this->name = $material->name;
        $this->unit = $material->unit;
        $this->minimum_stock = $material->minimum_stock;
        $this->showModal = true;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('confirm-dialog');
    }

    public function delete($id)
    {
        RawMaterial::find($id)->delete();
        $this->deleteId = null;
        $this->dispatch('notify', message: 'Raw material deleted successfully');
    }
}
