<?php

namespace App\Livewire\PriceListTypes;

use App\Models\PriceListType;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingId = null;
    public $deleteId = null;
    
    public $name = '';
    public $type = 'RETAIL';
    public $description = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|in:RETAIL,GROSIR,MEMBER,RESELLER',
        'description' => 'nullable|string',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->reset(['editingId', 'name', 'type', 'description']);
        $this->type = 'RETAIL';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['editingId', 'name', 'type', 'description']);
    }

    public function edit($id)
    {
        $priceListType = PriceListType::findOrFail($id);
        $this->editingId = $id;
        $this->name = $priceListType->name;
        $this->type = $priceListType->type;
        $this->description = $priceListType->description;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
        ];

        if ($this->editingId) {
            $priceListType = PriceListType::findOrFail($this->editingId);
            $data['updated_by'] = auth()->id();
            $priceListType->update($data);
            $this->dispatch('notify', message: 'Price list type updated successfully');
        } else {
            $data['created_by'] = auth()->id();
            PriceListType::create($data);
            $this->dispatch('notify', message: 'Price list type created successfully');
        }

        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('confirm-dialog');
    }

    public function delete($id)
    {
        PriceListType::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Price list type deleted successfully');
    }

    public function render()
    {
        $priceListTypes = PriceListType::query()
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.price-list-types.index', [
            'priceListTypes' => $priceListTypes,
            'types' => PriceListType::getTypes(),
        ])->layout('components.app-layout', ['title' => 'Price List Types']);
    }
}