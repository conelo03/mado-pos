<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    public const DEFAULT_CATEGORIES = [
        'Bahan Baku',
        'Gaji',
        'Sewa',
        'Listrik & Air',
        'Transportasi',
        'Perlengkapan',
        'Lainnya',
    ];

    protected $fillable = [
        'date',
        'category',
        'description',
        'amount',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
