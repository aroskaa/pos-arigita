<?php

namespace App\Livewire\CustomerOrders;

use App\Models\Customer;
use App\Models\CustomerOrder;
use App\Models\CustomerOrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CustomerOrderCreate extends Component
{
    public string $search = '';

    public array $cart = [];

    public string $customerType = 'personal';

    public string $customerName = '';

    public ?string $customerPhone = null;

    public ?string $customerAddress = null;

    public ?string $note = null;

    public bool $showSuccess = false;

    public ?string $createdOrderNumber = null;

    public function render()
    {
        return view('livewire.customer-orders.customer-order-create', [
            'products' => Product::query()
                ->with(['unit', 'prices'])
                ->where('is_active', true)
                ->when($this->search, function ($query) {
                    $query->where(function ($productQuery) {
                        $productQuery->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('sku', 'like', '%' . $this->search . '%')
                            ->orWhere('barcode', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('name')
                ->limit(18)
                ->get(),

            'estimatedTotal' => $this->estimatedTotal(),
        ]);
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

        DB::beginTransaction();

        try {
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

            $customer->update([
                'type' => $this->customerType,
                'name' => $this->customerName,
                'address' => $this->customerAddress,
            ]);

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
                CustomerOrderItem::query()->create([
                    'customer_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                    'unit_price' => (float) $item['unit_price'],
                    'subtotal' => (float) $item['subtotal'],
                ]);
            }

            DB::commit();

            $this->createdOrderNumber = $order->order_number;
            $this->showSuccess = true;

            $this->reset([
                'search',
                'cart',
                'customerType',
                'customerName',
                'customerPhone',
                'customerAddress',
                'note',
            ]);

            $this->customerType = 'personal';
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->addError('submit', $e->getMessage());
        }
    }

    public function estimatedTotal(): float
    {
        return collect($this->cart)->sum('subtotal');
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