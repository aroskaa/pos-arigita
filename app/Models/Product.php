<?php

namespace App\Models;

use App\Models\Category;
use App\Models\ProductPrice;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function promos(): BelongsToMany
    {
        return $this->belongsToMany(Promo::class, 'promo_product');
    }

    public function activePromo(): ?Promo
    {
        $basePrice = $this->getBasePriceForQuantity(1);

        return Promo::activeForProduct($this->id, $basePrice);
    }

    public function getBasePriceForQuantity(int $quantity): float
    {
        if ($this->relationLoaded('prices')) {
            return $this->resolveBasePriceFromCollection($quantity);
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

        // Tidak ada tier yang persis mencakup qty (mis. qty melebihi max tier terakhir):
        // pakai tier dengan min_qty terbesar yang masih <= qty, atau tier terkecil jika qty di bawah semua tier.
        $lastMatchingTier = $this->prices()
            ->where('min_qty', '<=', $quantity)
            ->orderByDesc('min_qty')
            ->first();

        if ($lastMatchingTier) {
            return (float) $lastMatchingTier->price;
        }

        $basePrice = $this->prices()->orderBy('min_qty', 'asc')->first();

        return (float) ($basePrice?->price ?? 0);
    }

    private function resolveBasePriceFromCollection(int $quantity): float
    {
        $matchingTier = $this->prices
            ->filter(function ($p) use ($quantity) {
                return $p->min_qty <= $quantity && (is_null($p->max_qty) || $p->max_qty >= $quantity);
            })
            ->sortByDesc('min_qty')
            ->first();

        if ($matchingTier) {
            return (float) $matchingTier->price;
        }

        $lastMatchingTier = $this->prices
            ->filter(fn ($p) => $p->min_qty <= $quantity)
            ->sortByDesc('min_qty')
            ->first();

        if ($lastMatchingTier) {
            return (float) $lastMatchingTier->price;
        }

        $basePrice = $this->prices->sortBy('min_qty')->first();

        return (float) ($basePrice?->price ?? 0);
    }

    public function applyActivePromo(float $basePrice): float
    {
        $promo = Promo::activeForProduct($this->id, $basePrice);

        if (! $promo) {
            return $basePrice;
        }

        $price = $promo->applyToPrice($basePrice);
        $floor = static::promoPriceFloor((float) $this->average_cost);

        // Harga promo minimal di atas HPP + margin, tetapi tidak pernah lebih mahal
        // dari harga dasar tier (promo tidak boleh menaikkan harga).
        return min(max($price, $floor), $basePrice);
    }

    public static function promoPriceFloor(float $hpp): float
    {
        if ($hpp <= 0) {
            return 0;
        }

        $marginRp = (float) Setting::get('min_margin_rp', 500);
        $marginPct = (float) Setting::get('min_margin_pct', 2);

        $margin = max($marginRp, $hpp * ($marginPct / 100));

        return Promo::roundUpToStep(round($hpp + $margin, 2));
    }

    public function getPriceForQuantity(int $quantity): float
    {
        $basePrice = $this->getBasePriceForQuantity($quantity);

        return $this->applyActivePromo($basePrice);
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
