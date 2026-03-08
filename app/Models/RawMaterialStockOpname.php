<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RawMaterialStockOpname extends Model
{
    use SoftDeletes;

    protected $table = 'raw_material_stock_opnames';

    protected $fillable = [
        'raw_material_id',
        'qty',
        'date',
        'note',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'date' => 'date',
    ];

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
