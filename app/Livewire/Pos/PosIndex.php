<?php

namespace App\Livewire\Pos;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class PosIndex extends Component
{
    public string $search = '';

    public array $cart = [];

    public bool $showConfirmModal = false;

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
            'subtotal' => $this->subtotal(),
        ]);
    }

    public function addToCart(int $productId): void
    {
        $product = Product::query()
            ->with('prices')
            ->findOrFail($productId);

        if ($product->stock <= 0) {
            session()->flash('error', 'Stok produk tidak tersedia.');
            return;
        }

        if (! isset($this->cart[$product->id])) {
            $this->cart[$product->id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'unit' => $product->unit?->abbreviation,
                'stock' => $product->stock,
                'quantity' => 1,
                'unit_price' => $product->getPriceForQuantity(1),
                'subtotal' => $product->getPriceForQuantity(1),
            ];

            return;
        }

        $this->increaseQuantity($product->id);
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

    public function openConfirmModal(): void
    {
        if (count($this->cart) === 0) {
            Session::flash('error', 'Keranjang masih kosong.');
            return;
        }

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

        DB::beginTransaction();

        try {
            $subtotal = $this->subtotal();

            $sale = Sale::query()->create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'customer_id' => null,
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

            $invoiceNumber = $sale->invoice_number;

            $this->clearCart();

            Session::flash(
                'success',
                "Transaksi berhasil disimpan. Invoice: {$invoiceNumber}"
            );

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

    private function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');

        $lastSale = Sale::query()
            ->whereDate('created_at', today())
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
