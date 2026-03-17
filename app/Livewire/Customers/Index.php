<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\PriceListType;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $priceListTypeFilter = '';
    public $showModal = false;
    public $editingId = null;
    public $deleteId = null;
    
    public $price_list_type_id = '';
    public $name = '';
    public $phone = '';
    public $address = '';

    protected $rules = [
        'price_list_type_id' => 'required|exists:price_list_types,id',
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPriceListTypeFilter()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->reset(['editingId', 'price_list_type_id', 'name', 'phone', 'address']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['editingId', 'price_list_type_id', 'name', 'phone', 'address']);
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $this->editingId = $id;
        $this->price_list_type_id = $customer->price_list_type_id;
        $this->name = $customer->name;
        $this->phone = $customer->phone;
        $this->address = $customer->address;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'price_list_type_id' => $this->price_list_type_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
        ];

        if ($this->editingId) {
            $customer = Customer::findOrFail($this->editingId);
            $data['updated_by'] = auth()->id();
            $customer->update($data);
            $this->dispatch('notify', message: 'Customer updated successfully');
        } else {
            $data['created_by'] = auth()->id();
            Customer::create($data);
            $this->dispatch('notify', message: 'Customer created successfully');
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
        Customer::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Customer deleted successfully');
    }

    public function render()
    {
        $query = Customer::with('priceListType')
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'));
        
        if ($this->priceListTypeFilter) {
            $query->where('price_list_type_id', $this->priceListTypeFilter);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(10);

        $priceListTypes = PriceListType::all();

        return view('livewire.customers.index', [
            'customers' => $customers,
            'priceListTypes' => $priceListTypes,
        ])->layout('components.app-layout', ['title' => 'Customers']);
    }
}