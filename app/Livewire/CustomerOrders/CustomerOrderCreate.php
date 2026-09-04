<?php

namespace App\Livewire\CustomerOrders;

use App\Models\Customer;
use App\Models\CustomerOrder;
use App\Models\CustomerOrderItem;
use App\Models\Product;
use App\Models\Promo;
use App\Models\SaleItem;
use App\Models\Setting;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerOrderCreate extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search = '';

    public bool $filterPromoOnly = false;

    public array $cart = [];

    public string $customerType = 'personal';

    public string $customerName = '';

    public ?string $customerPhone = null;

    public ?string $customerAddress = null;

    public ?string $note = null;

    public int $customerFormKey = 0;

    public bool $showSuccess = false;

    public ?string $createdOrderNumber = null;

    public int $minOrderTotal = 0;

    public int $minOrderQty = 0;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        $this->minOrderTotal = (int) Setting::get('min_order_total', 0);
        $this->minOrderQty = (int) Setting::get('min_order_qty', 0);

        $user = Auth::user();

        if (! $user || ! $user->isCustomer()) {
            return;
        }

        $customer = $user->customer;

        $this->customerType = $customer?->type ?? 'personal';
        $this->customerName = $customer?->name ?? $user->name;
        $this->customerPhone = $customer?->phone ?? $user->phone;
        $this->customerAddress = $customer?->address;
    }

    public function render()
    {
        $showFeatured = $this->search === '' && ! $this->filterPromoOnly;

        $featuredProducts = $showFeatured
            ? $this->featuredProducts()
            : collect();

        $products = Product::query()
            ->with(['unit', 'prices'])
            ->where('is_active', true)
            ->when($this->filterPromoOnly, function ($query): void {
                $query->whereHas('promos', function ($promoQuery): void {
                    $promoQuery->active();
                });
            })
            ->when($this->search, function ($query) {
                $query->where(function ($productQuery) {
                    $productQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%')
                        ->orWhere('barcode', 'like', '%' . $this->search . '%');
                });
            })
            ->when($showFeatured, fn ($query) => $query->whereNotIn('id', $featuredProducts->pluck('id')))
            ->orderBy('name')
            ->paginate(12);

        $bulkTierByProduct = [];

        $cardProducts = $products->getCollection()->merge($featuredProducts);

        foreach ($cardProducts as $product) {
            if ($product->prices->count() < 2) {
                continue;
            }

            $bestTier = $product->prices->sortByDesc('min_qty')->first();

            $bulkTierByProduct[$product->id] = [
                'min_qty' => (int) $bestTier->min_qty,
                'price' => $product->applyActivePromo((float) $bestTier->price),
            ];
        }

        $cartTierInfo = [];

        if (! empty($this->cart)) {
            $cartProducts = Product::query()
                ->with('prices')
                ->whereIn('id', array_keys($this->cart))
                ->get()
                ->keyBy('id');

            foreach ($this->cart as $productId => $item) {
                $product = $cartProducts->get((int) $productId);

                if (! $product || $product->prices->count() < 2) {
                    continue;
                }

                $prices = $product->prices->sortBy('min_qty');
                $quantity = (int) $item['quantity'];

                $matchedBulkTier = $prices->first(function ($p) use ($quantity) {
                    return $p->min_qty > 1
                        && $p->min_qty <= $quantity
                        && (is_null($p->max_qty) || $p->max_qty >= $quantity);
                });

                $nextTier = $prices->first(fn ($p) => $p->min_qty > $quantity);

                $cartTierInfo[(int) $productId] = [
                    'is_bulk' => (bool) $matchedBulkTier,
                    'tier_one_price' => $product->applyActivePromo((float) $prices->first()->price),
                    'next_min_qty' => $nextTier ? (int) $nextTier->min_qty : null,
                    'next_price' => $nextTier ? $product->applyActivePromo((float) $nextTier->price) : null,
                ];
            }
        }

        return view('livewire.customer-orders.customer-order-create', [
            'products' => $products,
            'featuredProducts' => $featuredProducts,
            'promoProductCount' => $this->promoProductCount(),
            'promoByProduct' => $this->promoByProduct($cardProducts),
            'bulkTierByProduct' => $bulkTierByProduct,
            'cartTierInfo' => $cartTierInfo,
            'estimatedTotal' => $this->estimatedTotal(),
            'totalQuantity' => $this->totalQuantity(),
        ]);
    }

    private function featuredProducts()
    {
        // Dipilih acak sekali per hari agar tidak berubah-ubah setiap kali halaman dirender.
        return cache()->remember(
            'customer-order-featured-' . now()->format('Ymd'),
            now()->endOfDay(),
            function () {
                $promoProductIds = Promo::query()
                    ->active()
                    ->with('products')
                    ->get()
                    ->flatMap(fn ($promo) => $promo->products->pluck('id'))
                    ->unique();

                $bestSellerIds = SaleItem::query()
                    ->selectRaw('product_id, SUM(quantity) as total_qty')
                    ->groupBy('product_id')
                    ->orderByDesc('total_qty')
                    ->limit(10)
                    ->pluck('product_id');

                return Product::query()
                    ->with(['unit', 'prices'])
                    ->where('is_active', true)
                    ->where(function ($query) use ($promoProductIds, $bestSellerIds): void {
                        $query->whereIn('id', $promoProductIds)
                            ->orWhereIn('id', $bestSellerIds);
                    })
                    ->inRandomOrder()
                    ->limit(3)
                    ->get();
            }
        );
    }

    private function promoProductCount(): int
    {
        return cache()->remember(
            'customer-order-promo-count-' . now()->format('Ymd'),
            now()->endOfDay(),
            fn () => Product::query()
                ->where('is_active', true)
                ->whereHas('promos', fn ($query) => $query->active())
                ->count()
        );
    }

    public function addToCart(int $productId): void
    {
        $product = Product::query()
            ->with(['unit', 'prices'])
            ->where('is_active', true)
            ->findOrFail($productId);

        if (! isset($this->cart[$product->id])) {
            $price = $product->getPriceForQuantity(1);

            $this->cart[$product->id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'unit' => $product->unit?->abbreviation,
                'quantity' => 1,
                'unit_price' => $price,
                'subtotal' => $price,
            ];

            $this->search = '';

            $this->dispatch('focus-customer-order-quantity', productId: $product->id);

            return;
        }

        $this->cart[$product->id]['quantity']++;

        $this->refreshCartItemPrice($product->id);

        $this->search = '';

        $this->dispatch('focus-customer-order-quantity', productId: $product->id);
    }

    public function increaseQuantity(int $productId): void
    {
        if (! isset($this->cart[$productId])) {
            return;
        }

        $this->cart[$productId]['quantity']++;

        $this->refreshCartItemPrice($productId);
    }

    public function decreaseQuantity(int $productId): void
    {
        if (! isset($this->cart[$productId])) {
            return;
        }

        if ($this->cart[$productId]['quantity'] <= 1) {
            $this->removeItem($productId);
            return;
        }

        $this->cart[$productId]['quantity']--;

        $this->refreshCartItemPrice($productId);
    }

    public function updateQuantity(int $productId, mixed $value): void
    {
        if (! isset($this->cart[$productId])) {
            return;
        }

        $quantity = (int) preg_replace('/\D/', '', (string) $value);

        if ($quantity <= 0) {
            $quantity = 1;
        }

        $this->cart[$productId]['quantity'] = $quantity;

        $this->refreshCartItemPrice($productId);
    }

    public function removeItem(int $productId): void
    {
        unset($this->cart[$productId]);
    }

    public function submitOrder(): void
    {
        $this->validate([
            'customerType' => ['required', 'in:personal,store'],
            'customerName' => ['required', 'string', 'max:255'],
            'customerPhone' => ['required', 'regex:/^[0-9]+$/', 'digits_between:8,15'],
            'customerAddress' => ['nullable', 'string', 'max:1000'],
            'note' => ['nullable', 'string', 'max:1000'],
            'cart' => ['required', 'array', 'min:1'],
        ], [
            'customerName.required' => 'Nama pelanggan atau nama toko wajib diisi.',
            'customerPhone.required' => 'Nomor HP wajib diisi.',
            'customerPhone.regex' => 'Nomor HP hanya boleh berisi angka.',
            'customerPhone.digits_between' => 'Nomor HP harus terdiri dari 8 sampai 15 digit.',
            'cart.required' => 'Minimal satu produk harus dipilih.',
            'cart.min' => 'Minimal satu produk harus dipilih.',
        ]);

        $recentOrder = CustomerOrder::query()
            ->where('customer_phone', $this->customerPhone)
            ->where('created_at', '>=', now()->subMinutes(2))
            ->exists();

        if ($recentOrder) {
            $this->addError(
                'submit',
                'Order dari nomor HP ini baru saja dikirim. Mohon tunggu sekitar 2 menit sebelum mengirim order kembali.'
            );

            return;
        }

        DB::beginTransaction();

        try {
            $user = Auth::user();

            if ($user && $user->isCustomer()) {
                $customer = Customer::query()
                    ->where('user_id', $user->id)
                    ->first();

                if (! $customer) {
                    $customer = Customer::query()
                        ->whereNull('user_id')
                        ->where('phone', $this->customerPhone)
                        ->first() ?? new Customer();
                }

                $customer->user_id = $user->id;

                $user->update([
                    'name' => $this->customerName,
                    'phone' => $this->customerPhone,
                ]);
            } else {
                $customer = Customer::query()->firstOrCreate(
                    [
                        'phone' => $this->customerPhone,
                    ],
                    [
                        'type' => $this->customerType,
                        'name' => $this->customerName,
                        'address' => $this->customerAddress,
                        'note' => null,
                    ]
                );
            }

            $customer->fill([
                'type' => $this->customerType,
                'name' => $this->customerName,
                'phone' => $this->customerPhone,
                'address' => $this->customerAddress,
            ])->save();

            $products = Product::query()
                ->whereIn('id', collect($this->cart)->pluck('product_id'))
                ->get()
                ->keyBy('id');

            $hasPreorderItems = false;

            $order = CustomerOrder::query()->create([
                'customer_id' => $customer->id,
                'order_number' => $this->generateOrderNumber(),
                'customer_type' => $this->customerType,
                'customer_name' => $this->customerName,
                'customer_phone' => $this->customerPhone,
                'customer_address' => $this->customerAddress,
                'status' => 'pending',
                'estimated_total' => $this->estimatedTotal(),
                'note' => $this->note,
            ]);

            foreach ($this->cart as $item) {
                $product = $products->get($item['product_id']);
                $requestedQuantity = (int) $item['quantity'];
                $availableQuantity = min($requestedQuantity, max(0, (int) ($product?->stock ?? 0)));
                $preorderQuantity = max(0, $requestedQuantity - $availableQuantity);

                if ($preorderQuantity > 0) {
                    $hasPreorderItems = true;
                }

                CustomerOrderItem::query()->create([
                    'customer_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $requestedQuantity,
                    'available_quantity' => $availableQuantity,
                    'preorder_quantity' => $preorderQuantity,
                    'unit_price' => (float) $item['unit_price'],
                    'subtotal' => (float) $item['subtotal'],
                ]);
            }

            if ($hasPreorderItems) {
                $order->update(['status' => 'preorder']);
            }

            ActivityLogger::log(
                'customer_order.created',
                "Order pelanggan {$order->order_number} masuk.",
                $order,
                [
                    'estimated_total' => (float) $order->estimated_total,
                    'item_count' => count($this->cart),
                    'status' => $order->status,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'user_id' => $user?->id,
                ],
            );

            DB::commit();

            $this->createdOrderNumber = $order->order_number;
            $this->showSuccess = true;

            $this->reset([
                'search',
                'cart',
                'note',
            ]);

            if ($user && $user->isCustomer()) {
                $this->customerType = $customer->type;
                $this->customerName = $customer->name;
                $this->customerPhone = $customer->phone;
                $this->customerAddress = $customer->address;
            } else {
                $this->reset([
                    'customerName',
                    'customerPhone',
                    'customerAddress',
                ]);

                $this->customerType = 'personal';
            }

            $this->customerFormKey++;

            if (! $user || ! $user->isCustomer()) {
                $this->dispatch('clear-customer-order-form');
            }
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->addError('submit', $e->getMessage());
        }
    }

    public function estimatedTotal(): float
    {
        return collect($this->cart)->sum('subtotal');
    }

    public function totalQuantity(): int
    {
        return (int) collect($this->cart)->sum('quantity');
    }

    public function meetsMinimumOrder(): bool
    {
        if ($this->minOrderTotal > 0 && $this->estimatedTotal() < $this->minOrderTotal) {
            return false;
        }

        if ($this->minOrderQty > 0 && $this->totalQuantity() < $this->minOrderQty) {
            return false;
        }

        return true;
    }

    private function promoByProduct($products): array
    {
        $activePromos = Promo::query()->active()->with('products')->get();

        if ($activePromos->isEmpty()) {
            return [];
        }

        $map = [];

        foreach ($products as $product) {
            $basePrice = $product->getBasePriceForQuantity(1);
            $bestPromo = null;
            $bestPrice = $basePrice;

            foreach ($activePromos as $promo) {
                if (! $promo->products->contains('id', $product->id)) {
                    continue;
                }

                $price = $promo->applyToPrice($basePrice);

                if ($price < $bestPrice) {
                    $bestPrice = $price;
                    $bestPromo = $promo;
                }
            }

            if ($bestPromo) {
                $floor = Product::promoPriceFloor((float) $product->average_cost);

                $map[$product->id] = [
                    'promo' => $bestPromo,
                    'original_price' => $basePrice,
                    'discounted_price' => min(max($bestPrice, $floor), $basePrice),
                ];
            }
        }

        return $map;
    }

    private function refreshCartItemPrice(int $productId): void
    {
        if (! isset($this->cart[$productId])) {
            return;
        }

        $product = Product::query()
            ->with('prices')
            ->findOrFail($productId);

        $quantity = (int) $this->cart[$productId]['quantity'];
        $price = $product->getPriceForQuantity($quantity);

        $this->cart[$productId]['unit_price'] = $price;
        $this->cart[$productId]['subtotal'] = $quantity * $price;
    }

    private function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');

        $lastOrder = CustomerOrder::query()
            ->whereDate('created_at', now()->toDateString())
            ->latest('id')
            ->first();

        $lastNumber = 0;

        if ($lastOrder) {
            $parts = explode('-', $lastOrder->order_number);

            $lastNumber = (int) end($parts);
        }

        $newNumber = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);

        return "ORD-{$date}-{$newNumber}";
    }
}
