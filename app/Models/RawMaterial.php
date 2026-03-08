<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RawMaterial extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'unit', 'stock', 'minimum_stock', 'created_by', 'updated_by'];

    protected $casts = [
        'stock' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
    ];

    public function boms(): HasMany
    {
        return $this->hasMany(Bom::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'boms')
            ->withPivot('qty')
            ->withTimestamps();
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(RawMaterialStockMovement::class);
    }

    public function stockInputs(): HasMany
    {
        return $this->hasMany(RawMaterialStockInput::class);
    }

    public function stockOpnames(): HasMany
    {
        return $this->hasMany(RawMaterialStockOpname::class);
    }
}
