<?php

namespace App\Models;

use App\Models\Category;
use App\Models\ProductPrice;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'unit_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'description',
        'image',
        'purchase_price',
        'average_cost',
        'stock',
        'minimum_stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'average_cost' => 'decimal:2',
            'stock' => 'integer',
            'minimum_stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function getSellingPriceAttribute(): float
    {
        return $this->getPriceForQuantity(1);
    }

    public function getPriceForQuantity(int $quantity): float
    {
        if ($this->relationLoaded('prices')) {
            $bulkPrice = $this->prices
                ->filter(function ($p) use ($quantity) {
                    return $p->min_qty <= $quantity && (is_null($p->max_qty) || $p->max_qty >= $quantity);
                })
                ->sortByDesc('min_qty')
                ->first();

            if ($bulkPrice) {
                return (float) $bulkPrice->price;
            }

            $basePrice = $this->prices->sortBy('min_qty')->first();
            return (float) ($basePrice?->price ?? 0);
        }

        $bulkPrice = $this->prices()
            ->where('min_qty', '<=', $quantity)
            ->where(function ($query) use ($quantity) {
                $query->whereNull('max_qty')
                    ->orWhere('max_qty', '>=', $quantity);
            })
            ->orderByDesc('min_qty')
            ->first();

        if ($bulkPrice) {
            return (float) $bulkPrice->price;
        }

        $basePrice = $this->prices()->orderBy('min_qty', 'asc')->first();

        return (float) ($basePrice?->price ?? 0);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function customerOrderItems(): HasMany
    {
        return $this->hasMany(CustomerOrderItem::class);
    }
}
