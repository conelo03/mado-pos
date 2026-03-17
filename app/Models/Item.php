<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'unit',
        'cost',
        'price',
        'stock',
        'minimum_stock',
        'is_active',
        'is_track_stock',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_track_stock' => 'boolean',
        'cost' => 'decimal:2',
        'price' => 'decimal:2',
        'stock' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
    ];

    public function boms()
    {
        return $this->hasMany(ItemBom::class, 'product_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function priceLists()
    {
        return $this->hasMany(ItemPriceList::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
