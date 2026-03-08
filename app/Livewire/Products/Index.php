<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingId = null;
    public $name = '';
    public $price = '';
    public $is_active = true;
    public $deleteId = null;

    public function render()
    {
        $products = Product::where('name', 'like', "%{$this->search}%")
            ->paginate(10);

        return view('livewire.products.index', [
            'products' => $products,
        ])->layout('components.app-layout', ['title' => 'Products']);
    }

    public function openModal()
    {
        $this->reset(['editingId', 'name', 'price', 'is_active']);
        $this->is_active = true;
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
            'price' => 'required|numeric|min:0',
        ]);

        if ($this->editingId) {
            $product = Product::find($this->editingId);
            $product->update([
                'name' => $this->name,
                'price' => $this->price,
                'is_active' => $this->is_active,
                'updated_by' => auth()->id(),
            ]);
        } else {
            Product::create([
                'name' => $this->name,
                'price' => $this->price,
                'is_active' => $this->is_active,
                'created_by' => auth()->id(),
            ]);
        }

        $this->closeModal();
        $this->dispatch('notify', message: 'Product saved successfully');
    }

    public function edit($id)
    {
        $product = Product::find($id);
        $this->editingId = $id;
        $this->name = $product->name;
        $this->price = $product->price;
        $this->is_active = (bool) $product->is_active;
        $this->showModal = true;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('confirm-dialog');
    }

    public function delete($id)
    {
        Product::find($id)->delete();
        $this->deleteId = null;
        $this->dispatch('notify', message: 'Product deleted successfully');
    }
}
