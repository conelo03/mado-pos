<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Bom;
use Livewire\Component;

class Detail extends Component
{
    public $productId;
    public $product;
    public $showBomModal = false;
    public $raw_material_id = '';
    public $qty = '';
    public $editingBomId = null;
    public $deleteBomId = null;

    public function mount($id)
    {
        $this->productId = $id;
        $this->product = Product::with('boms.rawMaterial')->find($id);
    }

    public function render()
    {
        $rawMaterials = RawMaterial::all();

        return view('livewire.products.detail', [
            'rawMaterials' => $rawMaterials,
        ])->layout('components.app-layout', ['title' => 'Product Detail']);
    }

    public function openBomModal()
    {
        $this->reset(['editingBomId', 'raw_material_id', 'qty']);
        $this->showBomModal = true;
    }

    public function closeBomModal()
    {
        $this->showBomModal = false;
    }

    public function saveBom()
    {
        $this->validate([
            'raw_material_id' => 'required|exists:raw_materials,id',
            'qty' => 'required|numeric|min:0.01',
        ]);

        if ($this->editingBomId) {
            $bom = Bom::find($this->editingBomId);
            $bom->update([
                'raw_material_id' => $this->raw_material_id,
                'qty' => $this->qty,
            ]);
        } else {
            Bom::create([
                'product_id' => $this->productId,
                'raw_material_id' => $this->raw_material_id,
                'qty' => $this->qty,
            ]);
        }

        $this->product = Product::with('boms.rawMaterial')->find($this->productId);
        $this->closeBomModal();
        $this->dispatch('notify', message: 'BOM saved successfully');
    }

    public function editBom($id)
    {
        $bom = Bom::find($id);
        $this->editingBomId = $id;
        $this->raw_material_id = $bom->raw_material_id;
        $this->qty = $bom->qty;
        $this->showBomModal = true;
    }

    public function confirmDeleteBom($id)
    {
        $this->deleteBomId = $id;
        $this->dispatch('confirm-dialog');
    }

    public function deleteBom($id)
    {
        Bom::find($id)->delete();
        $this->product = Product::with('boms.rawMaterial')->find($this->productId);
        $this->deleteBomId = null;
        $this->dispatch('notify', message: 'BOM deleted successfully');
    }
}
