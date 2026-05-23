<?php

namespace App\Livewire\Pos;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Livewire\Component;

use App\Models\CustomerOrder;

class PosIndex extends Component
{
    public string $search = '';

    public bool $showCustomerModal = false;

    public array $cart = [];

    public bool $showConfirmModal = false;

    public ?string $customerName = null;
    public ?string $customerPhone = null;
    public ?string $customerAddress = null;
    public string $customerType = 'personal';
    public ?string $customerNote = null;

    public ?int $selectedCustomerId = null;
    // public ?string $customerSearch = null;

    public ?int $loadedCustomerOrderId = null;

    public ?string $loadedCustomerOrderNumber = null;

    public array $orderLoadWarnings = [];

    public int $customerFormKey = 0;

    public string $barcodeBuffer = '';

    public function render()
    {
        return view('livewire.pos.pos-index', [
            'products' => Product::query()
                ->with(['category', 'unit', 'prices'])
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%')
                        ->orWhere('barcode', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->limit(12)
                ->get(),

            'customers' => Customer::query()
            ->when($this->customerName, function ($query) {
                $query->where('name', 'like', '%' . $this->customerName . '%')
                    ->orWhere('phone', 'like', '%' . $this->customerName . '%');
            })
            ->whereNotNull('name')
            ->latest()
            ->limit(5)
            ->get(),

            'subtotal' => $this->subtotal(),
        ]);
    }

    public function mount(): void
    {
        $customerOrderId = request()->query('customer_order');

        if ($customerOrderId) {
            $this->loadCustomerOrderToCart((int) $customerOrderId);
        }
    }

    public function addToCart(int $productId): void
    {
        $product = Product::query()
            ->with(['unit', 'prices'])
            ->findOrFail($productId);

        if (! $product->is_active) {
            Session::flash('error', 'Produk tidak aktif.');
            return;
        }

        if ($product->stock <= 0) {
            Session::flash('error', 'Stok produk tidak tersedia.');
            return;
        }

        if (! isset($this->cart[$product->id])) {
            $price = $product->getPriceForQuantity(1);

            $this->cart[$product->id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'unit' => $product->unit?->abbreviation,
                'stock' => $product->stock,
                'quantity' => 1,
                'unit_price' => $price,
                'subtotal' => $price,
            ];
        } else {
            if ($this->cart[$product->id]['quantity'] >= $this->cart[$product->id]['stock']) {
                Session::flash('error', 'Jumlah melebihi stok tersedia.');
                return;
            }

            $this->cart[$product->id]['quantity']++;

            $this->refreshCartItemPrice($product->id);
        }

        $this->reset('search');

        $this->dispatch('clear-product-search');

        $this->dispatch('focus-quantity', productId: $product->id);

        $this->dispatch('$refresh');
    }

    public function increaseQuantity(int $productId): void
    {
        if (! isset($this->cart[$productId])) {
            return;
        }

        if ($this->cart[$productId]['quantity'] >= $this->cart[$productId]['stock']) {
            session()->flash('error', 'Jumlah melebihi stok tersedia.');
            return;
        }

        $this->cart[$productId]['quantity']++;

        $this->refreshCartItemPrice($productId);

        $this->dispatch('$refresh');

        $this->dispatch('focus-quantity', productId: $productId);
    }

    public function decreaseQuantity(int $productId): void
    {
        if (! isset($this->cart[$productId])) {
            return;
        }

        if ($this->cart[$productId]['quantity'] <= 1) {
            unset($this->cart[$productId]);
            return;
        }

        $this->cart[$productId]['quantity']--;

        $this->refreshCartItemPrice($productId);

        $this->dispatch('$refresh');
    }

    public function removeItem(int $productId): void
    {
        unset($this->cart[$productId]);
    }

    public function clearCart(): void
    {
        $this->cart = [];
    }

    public function openCustomerModal(): void
    {
        $this->showCustomerModal = true;
    }

    // public function updatedCustomerPhone(): void
    // {
    //     $this->customerPhone = preg_replace('/\D/', '', (string) $this->customerPhone);
    // }

    public function closeCustomerModal(): void
    {
        $this->showCustomerModal = false;
    }

    public function saveCustomerData(): void
    {
        $this->validateCustomerData();

        $this->showCustomerModal = false;
    }

    public function selectCustomer(int $customerId): void
    {
        $customer = Customer::query()->findOrFail($customerId);

        $this->selectedCustomerId = $customer->id;
        $this->customerType = $customer->type;
        $this->customerName = $customer->name;
        $this->customerPhone = $customer->phone;
        $this->customerAddress = $customer->address;
        $this->customerNote = $customer->note;

        $this->customerFormKey++;
    }

    public function resetCustomer(): void
    {
        $this->reset([
            'selectedCustomerId',
            'customerName',
            'customerPhone',
            'customerAddress',
            'customerNote',
        ]);

        $this->customerType = 'personal';

        $this->customerFormKey++;
    }
    
    private function validateCustomerData(): void
    {
        if ($this->customerType === 'store') {
            $this->validate([
                'customerName' => ['required', 'string', 'max:255'],
                'customerPhone' => ['required', 'regex:/^[0-9]+$/', 'digits_between:8,15'],
                'customerAddress' => ['nullable', 'string', 'max:1000'],
            ], [
                'customerName.required' => 'Nama toko wajib diisi.',
                'customerPhone.required' => 'Nomor HP toko wajib diisi.',
                'customerPhone.regex' => 'Nomor HP hanya boleh berisi angka.',
                'customerPhone.digits_between' => 'Nomor HP harus terdiri dari 8 sampai 15 digit.',
            ]);

            return;
        }

        $this->validate([
            'customerName' => ['nullable', 'string', 'max:255'],
            'customerPhone' => ['nullable', 'regex:/^[0-9]+$/', 'digits_between:8,15'],
            'customerAddress' => ['nullable', 'string', 'max:1000'],
        ], [
            'customerPhone.regex' => 'Nomor HP hanya boleh berisi angka.',
            'customerPhone.digits_between' => 'Nomor HP harus terdiri dari 8 sampai 15 digit.',
        ]);
    }

    public function openConfirmModal(): void
    {
        if (count($this->cart) === 0) {
            Session::flash('error', 'Keranjang masih kosong.');
            return;
        }

        $this->validateCustomerData();

        $this->showConfirmModal = true;
    }

    public function closeConfirmModal(): void
    {
        $this->showConfirmModal = false;
    }

    public function saveTransaction(): void
    {
        $this->validateCustomerData();

        $this->showConfirmModal = false;

        if (count($this->cart) === 0) {
            Session::flash('error', 'Keranjang masih kosong.');
            return;
        }
        
        DB::beginTransaction();

        try {
            $subtotal = $this->subtotal();

            $customerId = $this->selectedCustomerId;

            if (! $customerId && $this->customerName) {
                $customer = Customer::query()->create([
                    'type' => $this->customerType,
                    'name' => $this->customerName,
                    'phone' => $this->customerPhone,
                    'address' => $this->customerAddress,
                    'note' => $this->customerNote,
                ]);

                $customerId = $customer->id;
            }

            $sale = Sale::query()->create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'customer_id' => $customerId,
                'customer_order_id' => $this->loadedCustomerOrderId,
                'cashier_id' => Auth::id(),
                'sale_date' => now(),
                'subtotal' => $this->subtotal(),
                'discount_total' => 0,
                'grand_total' => $this->subtotal(),
                'paid_amount' => $this->subtotal(),
                'change_amount' => 0,
                'payment_method' => 'cash',
                'status' => 'completed',
                'note' => null,
            ]);
            foreach ($this->cart as $item) {
                $product = Product::query()
                    ->lockForUpdate()
                    ->findOrFail($item['product_id']);

                if ($item['quantity'] > $product->stock) {
                    throw new \Exception(
                        "Stok {$product->name} tidak mencukupi."
                    );
                }

                SaleItem::query()->create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,

                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'cost_price' => $product->average_cost,
                    'subtotal' => $item['subtotal'],
                ]);

                $stockBefore = (int) $product->stock;
                $averageCostBefore = (float) $product->average_cost;

                $stockAfter = $stockBefore - (int) $item['quantity'];

                $product->update([
                    'stock' => $stockAfter,
                ]);

                StockMovement::query()->create([
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'quantity_in' => 0,
                    'quantity_out' => (int) $item['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'average_cost_before' => $averageCostBefore,
                    'average_cost_after' => $averageCostBefore,
                    'note' => 'Penjualan produk melalui POS.',
                    'created_by' => Auth::id(),
                ]);
            }

            if ($this->loadedCustomerOrderId) {
                CustomerOrder::query()
                    ->where('id', $this->loadedCustomerOrderId)
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'converted',
                        'converted_at' => now(),
                        'converted_by' => Auth::id(),
                    ]);
            }

            DB::commit();

            // $this->dispatch('focus-quantity', productId: $product->id);

            $invoiceNumber = $sale->invoice_number;

            $this->clearCart();

            Session::flash(
                'success',
                "Transaksi berhasil disimpan. Invoice: {$invoiceNumber}"
            );

            $this->resetCustomer();

            $this->reset([
                'loadedCustomerOrderId',
                'loadedCustomerOrderNumber',
                'orderLoadWarnings',
            ]);

            $this->customerType = 'personal';

            $this->dispatch('$refresh');

        } catch (\Throwable $e) {
            DB::rollBack();

            Session::flash(
                'error',
                $e->getMessage()
            );
        }
    }

    private function refreshCartItemPrice(int $productId): void
    {
        $product = Product::query()
            ->with('prices')
            ->findOrFail($productId);

        $quantity = $this->cart[$productId]['quantity'];
        $unitPrice = $product->getPriceForQuantity($quantity);

        $this->cart[$productId]['unit_price'] = $unitPrice;
        $this->cart[$productId]['subtotal'] = $unitPrice * $quantity;
    }

    public function subtotal(): float
    {
        return collect($this->cart)->sum('subtotal');
    }

    public function updateQuantity(int $productId, mixed $quantity): void
    {
        if (! isset($this->cart[$productId])) {
            return;
        }

        $quantity = (int) preg_replace('/\D/', '', (string) $quantity);

        if ($quantity <= 0) {
            unset($this->cart[$productId]);
            return;
        }

        if ($quantity > $this->cart[$productId]['stock']) {
            $quantity = $this->cart[$productId]['stock'];

            session()->flash('error', 'Jumlah disesuaikan dengan stok tersedia.');
        }

        $this->cart[$productId]['quantity'] = $quantity;

        $this->refreshCartItemPrice($productId);

        $this->dispatch('$refresh');
    }

    public function loadCustomerOrderToCart(int $customerOrderId): void
    {
        $order = CustomerOrder::query()
            ->with(['items.product.unit', 'items.product.prices', 'customer'])
            ->findOrFail($customerOrderId);

        if ($order->status !== 'pending') {
            Session::flash('error', 'Order pelanggan ini tidak dapat diproses karena statusnya bukan pending.');
            return;
        }

        $this->clearCart();

        $this->loadedCustomerOrderId = $order->id;
        $this->loadedCustomerOrderNumber = $order->order_number;
        $this->orderLoadWarnings = [];

        $this->selectedCustomerId = $order->customer_id;
        $this->customerType = $order->customer_type;
        $this->customerName = $order->customer_name;
        $this->customerPhone = $order->customer_phone;
        $this->customerAddress = $order->customer_address;
        $this->customerNote = $order->note;

        foreach ($order->items as $orderItem) {
            $product = $orderItem->product;

            if (! $product || ! $product->is_active) {
                $this->orderLoadWarnings[] = "Produk pada order tidak aktif atau tidak ditemukan.";
                continue;
            }

            if ($product->stock <= 0) {
                $this->orderLoadWarnings[] = "{$product->name} tidak dimasukkan ke cart karena stok kosong.";
                continue;
            }

            $requestedQuantity = (int) $orderItem->quantity;
            $cartQuantity = min($requestedQuantity, (int) $product->stock);

            if ($requestedQuantity > $product->stock) {
                $this->orderLoadWarnings[] = "{$product->name}: qty order {$requestedQuantity}, stok tersedia {$product->stock}. Qty cart disesuaikan menjadi {$cartQuantity}.";
            }

            $price = $product->getPriceForQuantity($cartQuantity);

            $this->cart[$product->id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'unit' => $product->unit?->abbreviation,
                'stock' => $product->stock,
                'quantity' => $cartQuantity,
                'unit_price' => $price,
                'subtotal' => $cartQuantity * $price,
            ];
        }

        if (count($this->cart) === 0) {
            Session::flash('error', 'Tidak ada item order yang dapat dimasukkan ke POS karena stok tidak tersedia.');
            return;
        }

        // Session::flash('success', "Order {$order->order_number} berhasil dimuat ke POS.");
    }

    #[On('barcode-scanned')]
    public function handleBarcodeScan(string $barcode): void
    {
        $barcode = preg_replace('/[^A-Za-z0-9]/', '', $barcode);

        if ($barcode === '') {
            return;
        }

        $product = Product::query()
            ->with(['unit', 'prices'])
            ->where('barcode', '=', $barcode)
            ->where('is_active', true)
            ->first();

        if (! $product) {
            Session::flash('error', "Produk dengan barcode {$barcode} tidak ditemukan.");
            return;
        }

        $this->addToCart($product->id);

        $this->search = '';
    }

    private function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');

        $lastSale = Sale::query()
            ->whereDate('created_at', '=', now()->toDateString())
            ->latest('id')
            ->first();

        $lastNumber = 0;

        if ($lastSale) {
            $parts = explode('-', $lastSale->invoice_number);

            $lastNumber = (int) end($parts);
        }

        $newNumber = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);

        return "INV-{$date}-{$newNumber}";
    }
}
