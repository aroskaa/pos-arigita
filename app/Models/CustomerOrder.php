<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerOrder extends Model
{
    protected $fillable = [
        'customer_id',
        'order_number',
        'customer_type',
        'customer_name',
        'customer_phone',
        'customer_address',
        'status',
        'estimated_total',
        'note',
        'converted_at',
        'converted_by',
        'rejected_at',
        'rejected_by',
        'rejection_note',
        'cancelled_at',
        'cancelled_by',
        'cancel_note',
    ];

    protected function casts(): array
    {
        return [
            'estimated_total' => 'decimal:2',
            'converted_at' => 'datetime',
            'rejected_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CustomerOrderItem::class);
    }

    public function converter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'converted_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function sale(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
