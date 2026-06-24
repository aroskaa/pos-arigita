<?php

namespace App\Livewire\StockAdjustments;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\ActivityLogger;

class StockAdjustmentIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public ?string $startDate = null;

    public ?string $endDate = null;

    public int $dateFilterKey = 0;

    public bool $showModal = false;

    public string $productSearch = '';

    public ?int $selectedProductId = null;

    public ?array $selectedProduct = null;

    public ?int $systemStock = null;

    public ?int $physicalStock = null;

    public ?int $difference = null;

    public ?string $note = null;

    protected string $paginationTheme = 'tailwind';

    public function render()
    {
        return view('livewire.stock-adjustments.stock-adjustment-index', [
            'adjustments' => StockMovement::query()
                ->with(['product', 'creator'])
                ->where('type', 'adjustment')
                ->when($this->search, function ($query) {
                    $query->whereHas('product', function ($productQuery) {
                        $productQuery->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('sku', 'like', '%' . $this->search . '%')
                            ->orWhere('barcode', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->startDate, function ($query) {
                    $query->whereDate('created_at', '>=', $this->startDate);
                })
                ->when($this->endDate, function ($query) {
                    $query->whereDate('created_at', '<=', $this->endDate);
                })
                ->latest()
                ->paginate(10),

            'productSuggestions' => Product::query()
                ->where('is_active', true)
                ->when($this->productSearch, function ($query) {
                    $query->where(function ($productQuery) {
                        $productQuery->where('name', 'like', '%' . $this->productSearch . '%')
                            ->orWhere('sku', 'like', '%' . $this->productSearch . '%')
                            ->orWhere('barcode', 'like', '%' . $this->productSearch . '%');
                    });
                })
                ->orderBy('name')
                ->limit(8)
                ->get(),
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    
    public function updatedStartDate(): void
    {
        $this->resetPage();
    }

    public function updatedEndDate(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->startDate = null;
        $this->endDate = null;

        $this->dateFilterKey++;

        $this->resetPage();

        $this->dispatch('clear-stock-adjustment-date-filters');
    }

    public function updatedPhysicalStock(): void
    {
        $this->calculateDifference();
    }

    public function create(): void
    {
        $this->resetForm();

        $this->showModal = true;

        $this->dispatch('focus-adjustment-product');
    }

    public function selectProduct(int $productId): void
    {
        $product = Product::query()->findOrFail($productId);

        $this->selectedProductId = $product->id;
        $this->systemStock = (int) $product->stock;
        $this->physicalStock = (int) $product->stock;
        $this->difference = 0;

        $this->selectedProduct = [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'stock' => $product->stock,
            'unit' => $product->unit?->abbreviation,
            'average_cost' => (float) $product->average_cost,
        ];

        $this->productSearch = $product->name;

        $this->dispatch('focus-adjustment-physical-stock');
    }

    public function selectFirstProduct(): void
    {
        if (! $this->productSearch) {
            return;
        }

        $product = Product::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->productSearch . '%')
                    ->orWhere('sku', 'like', '%' . $this->productSearch . '%')
                    ->orWhere('barcode', 'like', '%' . $this->productSearch . '%');
            })
            ->orderBy('name')
            ->first();

        if (! $product) {
            $this->addError('selectedProductId', 'Produk tidak ditemukan.');
            return;
        }

        $this->selectProduct($product->id);
    }

    public function saveAdjustment(): void
    {
        $this->validate([
            'selectedProductId' => ['required', 'exists:products,id'],
            'physicalStock' => ['required', 'integer', 'min:0'],
            'note' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'selectedProductId.required' => 'Produk wajib dipilih.',
            'physicalStock.required' => 'Stok fisik wajib diisi.',
            'physicalStock.integer' => 'Stok fisik harus berupa angka.',
            'physicalStock.min' => 'Stok fisik tidak boleh kurang dari 0.',
            'note.required' => 'Alasan adjustment wajib diisi.',
            'note.min' => 'Alasan adjustment minimal 5 karakter.',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($this->selectedProductId);

            $stockBefore = (int) $product->stock;
            $stockAfter = (int) $this->physicalStock;
            $difference = $stockAfter - $stockBefore;

            if ($difference === 0) {
                $this->addError('physicalStock', 'Stok fisik sama dengan stok sistem, tidak ada adjustment yang perlu disimpan.');

                DB::rollBack();

                return;
            }

            $averageCost = (float) $product->average_cost;

            $product->update([
                'stock' => $stockAfter,
            ]);

            $movement = StockMovement::query()->create([
                'product_id' => $product->id,
                'type' => 'adjustment',
                'reference_type' => Product::class,
                'reference_id' => $product->id,
                'quantity_in' => $difference > 0 ? $difference : 0,
                'quantity_out' => $difference < 0 ? abs($difference) : 0,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'average_cost_before' => $averageCost,
                'average_cost_after' => $averageCost,
                'note' => $this->note,
                'created_by' => Auth::id(),
            ]);

            ActivityLogger::log(
                'stock.adjusted',
                "Stock adjustment {$product->name}: {$stockBefore} menjadi {$stockAfter}.",
                $movement,
                [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'difference' => $difference,
                ],
            );

            DB::commit();

            Session::flash('success', 'Stock adjustment berhasil disimpan.');

            $this->showModal = false;

            $this->resetForm();

            $this->dispatch('$refresh');
        } catch (\Throwable $e) {
            DB::rollBack();

            Session::flash('error', $e->getMessage());
        }
    }

    private function calculateDifference(): void
    {
        if ($this->systemStock === null || $this->physicalStock === null) {
            $this->difference = null;
            return;
        }

        $this->difference = (int) $this->physicalStock - (int) $this->systemStock;
    }

    private function resetForm(): void
    {
        $this->reset([
            'productSearch',
            'selectedProductId',
            'selectedProduct',
            'systemStock',
            'physicalStock',
            'difference',
            'note',
        ]);

        $this->resetValidation();
    }
}
