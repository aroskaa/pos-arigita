<?php

namespace App\Livewire\Pos;

use App\Models\Product;
use Livewire\Component;

class PosIndex extends Component
{
    public string $search = '';

    public array $cart = [];

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
}
