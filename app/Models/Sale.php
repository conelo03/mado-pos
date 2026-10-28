<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'price_list_type_id',
        'invoice_no',
        'total_cost',
        'total_price',
        'paid_amount',
        'change_amount',
        'discount',
        'payment_method',
        'status',
        'refunded_at',
        'refunded_by',
        'cancelled_at',
        'cancelled_by',
        'status_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_cost' => 'decimal:2',
        'total_price' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'refunded_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public static function paymentMethodLabel(?string $method): string
    {
        return $method === 'NON_CASH' ? __('Non-cash') : __('Cash');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function priceListType(): BelongsTo
    {
        return $this->belongsTo(PriceListType::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function refundedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}
