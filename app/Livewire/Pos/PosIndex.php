<?php

namespace App\Livewire\Pos;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Livewire\Component;

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

    public function updatedCustomerPhone(): void
    {
        $this->customerPhone = preg_replace('/\D/', '', (string) $this->customerPhone);
    }

    public function closeCustomerModal(): void
    {
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
                'customerPhone' => ['required', 'digits_between:8,15'],
                'customerAddress' => ['nullable', 'string', 'max:1000'],
            ], [
                'customerName.required' => 'Nama toko wajib diisi.',
                'customerPhone.required' => 'Nomor HP toko wajib diisi.',
                'customerPhone.digits_between' => 'Nomor HP harus terdiri dari 8 sampai 15 digit.',
            ]);

            return;
        }

        if ($this->customerName || $this->customerPhone || $this->customerAddress) {
            $this->validate([
                'customerName' => ['nullable', 'string', 'max:255'],
                'customerPhone' => ['nullable', 'digits_between:8,15'],
                'customerAddress' => ['nullable', 'string', 'max:1000'],
            ], [
                'customerPhone.digits_between' => 'Nomor HP harus terdiri dari 8 sampai 15 digit.',
            ]);
        }
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
        $this->showConfirmModal = false;
        
        if (count($this->cart) === 0) {
            Session::flash('error', 'Keranjang masih kosong.');

            return;
        }

        $this->validateCustomerData();
        
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
                'cashier_id' => Auth::id(),
                'sale_date' => now(),

                'subtotal' => $subtotal,
                'discount_total' => 0,
                'grand_total' => $subtotal,

                'paid_amount' => $subtotal,
                'change_amount' => 0,

                'payment_method' => 'cash',
                'status' => 'completed',
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

                $product->decrement('stock', $item['quantity']);
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
