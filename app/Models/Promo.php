<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Promo extends Model
{
    protected $fillable = [
        'name',
        'type',
        'value',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'promo_product');
    }

    public function scopeActive($query)
    {
        $now = Carbon::now();

        return $query
            ->where('is_active', true)
            ->where('starts_at', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            });
    }

    public static function activeForProduct(int $productId, float $basePrice = 0): ?self
    {
        return static::active()
            ->whereHas('products', function ($q) use ($productId) {
                $q->where('products.id', $productId);
            })
            ->get()
            ->sortBy(fn (self $promo) => $promo->applyToPrice($basePrice))
            ->first();
    }

    public function applyToPrice(float $price): float
    {
        $discounted = $this->type === 'percent'
            ? $price * (1 - ((float) $this->value / 100))
            : $price - (float) $this->value;

        // Bulatkan ke atas ke kelipatan pembulatan harga (default Rp 100)
        // agar mudah dihitung kembaliannya saat transaksi.
        return (float) static::roundUpToStep(max(0, round($discounted, 2)));
    }

    public static function roundUpToStep(float $price): float
    {
        $step = (float) Setting::get('price_round_step', 100);

        if ($step <= 1) {
            return (float) ceil($price);
        }

        return (float) (ceil($price / $step) * $step);
    }

    public function discountLabel(): string
    {
        return $this->type === 'percent'
            ? 'Diskon ' . rtrim(rtrim(number_format((float) $this->value, 2), '0'), '.') . '%'
            : 'Diskon Rp ' . number_format((float) $this->value, 0, ',', '.');
    }
}
