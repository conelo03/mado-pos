<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemBom extends Model
{
    use HasFactory;

    protected $table = 'item_boms';

    protected $fillable = [
        'product_id',
        'material_id',
        'qty',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Item::class, 'product_id');
    }

    public function material()
    {
        return $this->belongsTo(Item::class, 'material_id');
    }
}
