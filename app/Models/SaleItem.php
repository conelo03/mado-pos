<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use SoftDeletes;

    protected $fillable = ['sale_id', 'item_id', 'price', 'cost', 'qty', 'subtotal', 'cost_subtotal'];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'qty' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'cost_subtotal' => 'decimal:2',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
