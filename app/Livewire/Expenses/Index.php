<?php

namespace App\Livewire\Expenses;

use App\Models\Expense;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $startDate = '';
    public $endDate = '';
    public $showModal = false;
    public $editingId = null;
    public $deleteId = null;

    public $date = '';
    public $category = '';
    public $description = '';
    public $amount = '';

    protected $rules = [
        'date' => 'required|date',
        'category' => 'required|string|max:100',
        'description' => 'nullable|string|max:255',
        'amount' => 'required|numeric|gt:0',
    ];

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'categoryFilter', 'startDate', 'endDate'])) {
            $this->resetPage();
        }
    }

    public function openModal()
    {
        $this->reset(['editingId', 'category', 'description', 'amount']);
        $this->date = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['editingId', 'date', 'category', 'description', 'amount']);
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $this->editingId = $id;
        $this->date = $expense->date->format('Y-m-d');
        $this->category = $expense->category;
        $this->description = $expense->description;
        $this->amount = $expense->amount;
        $this->showModal = true;
    }

    public function save()
    {
        $this->category = trim((string) $this->category);
        $this->validate();

        $data = [
            'date' => $this->date,
            'category' => $this->category,
            'description' => trim((string) $this->description) ?: null,
            'amount' => $this->amount,
        ];

        if ($this->editingId) {
            $data['updated_by'] = auth()->id();
            Expense::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', message: __('Expense updated successfully'));
        } else {
            $data['created_by'] = auth()->id();
            Expense::create($data);
            $this->dispatch('notify', message: __('Expense created successfully'));
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
        Expense::findOrFail($id)->delete();
        $this->deleteId = null;
        $this->dispatch('notify', message: __('Expense deleted successfully'));
    }

    public function render()
    {
        $query = Expense::query()
            ->when($this->startDate, fn ($q) => $q->whereDate('date', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->whereDate('date', '<=', $this->endDate))
            ->when($this->categoryFilter, fn ($q) => $q->where('category', $this->categoryFilter))
            ->when($this->search, fn ($q) => $q->where('description', 'like', '%' . $this->search . '%'));

        $totalAmount = (clone $query)->sum('amount');
        $expenses = $query->with('createdBy')->orderByDesc('date')->orderByDesc('id')->paginate(10);

        $categories = collect(Expense::DEFAULT_CATEGORIES)
            ->merge(Expense::query()->distinct()->pluck('category'))
            ->unique()
            ->sort()
            ->values();

        return view('livewire.expenses.index', [
            'expenses' => $expenses,
            'totalAmount' => $totalAmount,
            'categories' => $categories,
        ])->layout('components.app-layout', ['title' => __('Expenses')]);
    }
}
