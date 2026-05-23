<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'reference_type',
        'reference_id',
        'quantity_in',
        'quantity_out',
        'stock_before',
        'stock_after',
        'average_cost_before',
        'average_cost_after',
        'note',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity_in' => 'integer',
            'quantity_out' => 'integer',
            'stock_before' => 'integer',
            'stock_after' => 'integer',
            'average_cost_before' => 'decimal:2',
            'average_cost_after' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
