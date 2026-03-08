<?php

namespace App\Livewire\Items;

use App\Models\Item;
use App\Models\ItemBom;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class Detail extends Component
{
    use WithPagination;

    public $itemId;
    public $item;
    public $showBomModal = false;
    public $editingBomId = null;
    public $material_id = '';
    public $bom_qty = '';
    public $deletingBomId = null;
    public $dateFrom = '';
    public $dateTo = '';

    public function mount($id)
    {
        $this->itemId = $id;
        $this->item = Item::find($id);
        
        if (!$this->item) {
            abort(404);
        }
    }

    public function render()
    {
        $boms = $this->item->type === 'PRODUCT' 
            ? ItemBom::where('product_id', $this->itemId)->with('material')->get()
            : [];

        $movementsQuery = StockMovement::where('item_id', $this->itemId);
        
        if ($this->dateFrom) {
            $movementsQuery->whereDate('created_at', '>=', $this->dateFrom);
        }
        
        if ($this->dateTo) {
            $movementsQuery->whereDate('created_at', '<=', $this->dateTo);
        }

        $movements = $movementsQuery->latest()->paginate(10);

        return view('livewire.items.detail', [
            'boms' => $boms,
            'movements' => $movements,
        ])->layout('components.app-layout', ['title' => $this->item->name]);
    }

    public function openBomModal()
    {
        $this->reset(['editingBomId', 'material_id', 'bom_qty']);
        $this->showBomModal = true;
    }

    public function closeBomModal()
    {
        $this->showBomModal = false;
    }

    public function saveBom()
    {
        $this->validate([
            'material_id' => 'required|exists:items,id',
            'bom_qty' => 'required|numeric|min:0.01',
        ]);

        if ($this->editingBomId) {
            $bom = ItemBom::find($this->editingBomId);
            $bom->update([
                'material_id' => $this->material_id,
                'qty' => $this->bom_qty,
            ]);
        } else {
            ItemBom::create([
                'product_id' => $this->itemId,
                'material_id' => $this->material_id,
                'qty' => $this->bom_qty,
            ]);
        }

        $this->closeBomModal();
        $this->dispatch('notify', message: 'BOM saved successfully');
    }

    public function editBom($id)
    {
        $bom = ItemBom::find($id);
        $this->editingBomId = $id;
        $this->material_id = $bom->material_id;
        $this->bom_qty = $bom->qty;
        $this->showBomModal = true;
    }

    public function confirmDeleteBom($id)
    {
        $this->deletingBomId = $id;
        $this->dispatch('confirm-dialog');
    }

    public function deleteBom($id)
    {
        ItemBom::find($id)->delete();
        $this->dispatch('notify', message: 'BOM deleted successfully');
    }

    public function resetFilter()
    {
        $this->reset(['dateFrom', 'dateTo']);
    }
}
