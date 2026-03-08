<?php

namespace App\Livewire\RawMaterials;

use App\Models\RawMaterial;
use App\Models\RawMaterialStockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class Detail extends Component
{
    use WithPagination;

    public $materialId;
    public $material;
    public $dateFrom = '';
    public $dateTo = '';

    public function mount($id)
    {
        $this->materialId = $id;
        $this->material = RawMaterial::find($id);
        $this->dateFrom = date('Y-m-d', strtotime('-30 days'));
        $this->dateTo = date('Y-m-d');
    }

    public function render()
    {
        $query = RawMaterialStockMovement::where('raw_material_id', $this->materialId);

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $movements = $query->latest()->paginate(20);

        return view('livewire.raw-materials.detail', [
            'movements' => $movements,
        ])->layout('components.app-layout', ['title' => 'Raw Material Detail']);
    }

    public function resetFilter()
    {
        $this->dateFrom = date('Y-m-d', strtotime('-30 days'));
        $this->dateTo = date('Y-m-d');
    }
}
