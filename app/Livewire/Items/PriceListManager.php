<?php

namespace App\Livewire\Items;

use App\Models\Item;
use App\Models\ItemPriceList;
use App\Models\PriceListType;
use Livewire\Component;

class PriceListManager extends Component
{
    public $itemId;
    public $item;
    public $showModal = false;
    public $editingId = null;
    public $deleteId = null;
    
    public $price_list_type_id = '';
    public $price = '';

    protected $rules = [
        'price_list_type_id' => 'required|exists:price_list_types,id',
        'price' => 'required|numeric|min:0',
    ];

    public function mount($itemId)
    {
        $this->itemId = $itemId;
        $this->item = Item::findOrFail($itemId);
        
        // Only allow PRODUCT items
        if ($this->item->type !== 'PRODUCT') {
            abort(403, 'Price lists can only be managed for PRODUCT items');
        }
    }

    public function openModal()
    {
        $this->reset(['editingId', 'price_list_type_id', 'price']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['editingId', 'price_list_type_id', 'price']);
    }

    public function edit($id)
    {
        $priceList = ItemPriceList::findOrFail($id);
        $this->editingId = $id;
        $this->price_list_type_id = $priceList->price_list_type_id;
        $this->price = $priceList->price;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'item_id' => $this->itemId,
            'price_list_type_id' => $this->price_list_type_id,
            'price' => $this->price,
        ];

        if ($this->editingId) {
            $priceList = ItemPriceList::findOrFail($this->editingId);
            $data['updated_by'] = auth()->id();
            $priceList->update($data);
            $this->dispatch('notify', message: 'Price list updated successfully');
        } else {
            $data['created_by'] = auth()->id();
            ItemPriceList::create($data);
            $this->dispatch('notify', message: 'Price list created successfully');
        }

        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('confirm-price-list-delete');
    }

    public function delete($id)
    {
        ItemPriceList::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Price list deleted successfully');
    }

    public function render()
    {
        $priceLists = ItemPriceList::with('priceListType')
            ->where('item_id', $this->itemId)
            ->get();

        $availablePriceListTypes = PriceListType::whereNotIn('id', 
            $priceLists->pluck('price_list_type_id')->toArray()
        )->get();

        return view('livewire.items.price-list-manager', [
            'priceLists' => $priceLists,
            'availablePriceListTypes' => $availablePriceListTypes,
            'allPriceListTypes' => PriceListType::all(),
        ]);
    }
}