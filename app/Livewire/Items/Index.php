<?php

namespace App\Livewire\Items;

use App\Models\Item;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';
    public $showModal = false;
    public $editingId = null;
    public $name = '';
    public $type = 'PRODUCT';
    public $unit = '';
    public $price = '';
    public $minimum_stock = '';
    public $is_active = true;
    public $is_track_stock = true;
    public $deleteId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|in:PRODUCT,RAW_MATERIAL',
        'unit' => 'required|string|max:50',
        'price' => 'required|numeric|min:0',
        'minimum_stock' => 'required|numeric|min:0',
    ];

    public function render()
    {
        $query = Item::where('name', 'like', "%{$this->search}%");
        
        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        $items = $query->paginate(10);

        return view('livewire.items.index', [
            'items' => $items,
        ])->layout('components.app-layout', ['title' => 'Items']);
    }

    public function openModal()
    {
        $this->reset(['editingId', 'name', 'type', 'unit', 'price', 'minimum_stock', 'is_active', 'is_track_stock']);
        $this->type = 'PRODUCT';
        $this->is_active = true;
        $this->is_track_stock = true;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            $item = Item::find($this->editingId);
            $item->update([
                'name' => $this->name,
                'type' => $this->type,
                'unit' => $this->unit,
                'price' => $this->price,
                'minimum_stock' => $this->minimum_stock,
                'is_active' => $this->is_active,
                'is_track_stock' => $this->is_track_stock,
                'updated_by' => auth()->id(),
            ]);
        } else {
            Item::create([
                'name' => $this->name,
                'type' => $this->type,
                'unit' => $this->unit,
                'price' => $this->price,
                'stock' => 0,
                'minimum_stock' => $this->minimum_stock,
                'is_active' => $this->is_active,
                'is_track_stock' => $this->is_track_stock,
                'created_by' => auth()->id(),
            ]);
        }

        $this->closeModal();
        $this->dispatch('notify', message: 'Item saved successfully');
    }

    public function edit($id)
    {
        $item = Item::find($id);
        $this->editingId = $id;
        $this->name = $item->name;
        $this->type = $item->type;
        $this->unit = $item->unit;
        $this->price = $item->price;
        $this->minimum_stock = $item->minimum_stock;
        $this->is_active = (bool) $item->is_active;
        $this->is_track_stock = (bool) $item->is_track_stock;
        $this->showModal = true;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('confirm-dialog');
    }

    public function delete($id)
    {
        Item::find($id)->delete();
        $this->deleteId = null;
        $this->dispatch('notify', message: 'Item deleted successfully');
    }
}
