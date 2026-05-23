<?php

namespace App\Livewire\StockMovements;

use App\Models\Product;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class StockMovementIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $productId = null;

    public string $type = '';

    protected string $paginationTheme = 'tailwind';

    public function render()
    {
        return view('livewire.stock-movements.stock-movement-index', [
            'movements' => StockMovement::query()
                ->with(['product', 'creator'])
                ->when($this->search, function ($query) {
                    $query->whereHas('product', function ($productQuery) {
                        $productQuery->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('sku', 'like', '%' . $this->search . '%')
                            ->orWhere('barcode', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->productId, function ($query) {
                    $query->where('product_id', $this->productId);
                })
                ->when($this->type, function ($query) {
                    $query->where('type', $this->type);
                })
                ->latest()
                ->paginate(15),

            'products' => Product::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),

            'movementTypes' => [
                'purchase' => 'Pembelian',
                'sale' => 'Penjualan',
                'adjustment' => 'Adjustment',
                'return' => 'Return',
                'order_cancel' => 'Pembatalan Order',
            ],
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedProductId(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'productId',
            'type',
        ]);

        $this->resetPage();
    }
}
