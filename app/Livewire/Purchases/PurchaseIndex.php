<?php

namespace App\Livewire\Purchases;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $supplier_id = null;

    public string $supplierSearch = '';

    public ?array $selectedSupplier = null;

    public ?string $purchase_date = null;

    public ?string $note = null;

    public ?int $selectedProductId = null;
    
    public string $productSearch = '';

    public ?array $selectedProduct = null;

    public bool $showDetailModal = false;

    public array $detailPurchase = [];

    public int $quantity = 1;

    public ?string $unit_cost = null;

    public array $items = [];

    protected string $paginationTheme = 'tailwind';

    

    public function mount(): void
    {
        $this->purchase_date = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.purchases.purchase-index', [
            'purchases' => Purchase::query()
                ->with(['supplier', 'creator', 'items'])
                ->when($this->search, function ($query) {
                    $query->where('invoice_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('supplier', function ($supplierQuery) {
                            $supplierQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                })
                ->latest()
                ->paginate(10),

            'supplierSuggestions' => Supplier::query()
                ->where('is_active', true)
                ->when($this->supplierSearch, function ($query) {
                    $query->where(function ($supplierQuery) {
                        $supplierQuery->where('name', 'like', '%' . $this->supplierSearch . '%')
                            ->orWhere('phone', 'like', '%' . $this->supplierSearch . '%')
                            ->orWhere('address', 'like', '%' . $this->supplierSearch . '%');
                    });
                })
                ->orderBy('name')
                ->limit(8)
                ->get(),

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

            'totalAmount' => $this->totalAmount(),
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();

        $this->showModal = true;

        $this->dispatch('focus-purchase-supplier');
    }

    public function selectSupplier(int $supplierId): void
    {
        $supplier = Supplier::query()
            ->where('is_active', true)
            ->findOrFail($supplierId);

        $this->supplier_id = $supplier->id;

        $this->selectedSupplier = [
            'id' => $supplier->id,
            'name' => $supplier->name,
            'phone' => $supplier->phone,
        ];

        $this->supplierSearch = $supplier->name;

        $this->dispatch('focus-purchase-date');
    }

    public function selectFirstSupplierOrSkip(): void
    {
        if (! $this->supplierSearch) {
            $this->supplier_id = null;
            $this->selectedSupplier = null;

            $this->dispatch('focus-purchase-date');

            return;
        }

        $supplier = Supplier::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->supplierSearch . '%')
                    ->orWhere('phone', 'like', '%' . $this->supplierSearch . '%')
                    ->orWhere('address', 'like', '%' . $this->supplierSearch . '%');
            })
            ->orderBy('name')
            ->first();

        if (! $supplier) {
            $this->supplier_id = null;
            $this->selectedSupplier = null;

            $this->dispatch('focus-purchase-date');

            return;
        }

        $this->selectSupplier($supplier->id);
    }

    public function selectProduct(int $productId): void
    {
        $product = Product::query()->findOrFail($productId);

        $this->selectedProductId = $product->id;

        $this->selectedProduct = [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'stock' => $product->stock,
        ];

        $this->productSearch = $product->name;

        $this->dispatch('focus-purchase-qty');
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

    public function addItem(): void
    {
        $this->resetErrorBag([
            'selectedProductId',
            'quantity',
            'unit_cost',
        ]);

        $this->validate([
            'selectedProductId' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ], [
            'selectedProductId.required' => 'Produk wajib dipilih.',
            'quantity.required' => 'Jumlah pembelian wajib diisi.',
            'quantity.min' => 'Jumlah pembelian minimal 1.',
        ]);

        $unitCost = $this->currencyToNumber($this->unit_cost);

        if ($unitCost <= 0) {
            $this->addError('unit_cost', 'Harga beli wajib diisi.');
            return;
        }

        $product = Product::query()->findOrFail($this->selectedProductId);

        $productId = $product->id;
        $quantity = (int) $this->quantity;

        if (isset($this->items[$productId])) {
            $this->items[$productId]['quantity'] += $quantity;
            $this->items[$productId]['unit_cost'] = $unitCost;
            $this->items[$productId]['subtotal'] = $this->items[$productId]['quantity'] * $unitCost;
        } else {
            $this->items[$productId] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'subtotal' => $quantity * $unitCost,
            ];
        }

        $this->selectedProductId = null;
        $this->selectedProduct = null;
        $this->productSearch = '';
        $this->quantity = 1;
        $this->unit_cost = null;

        $this->dispatch('clear-purchase-item-form');
        $this->dispatch('focus-purchase-product');
        $this->dispatch('$refresh');
    }

    public function removeItem(int $productId): void
    {
        unset($this->items[$productId]);
    }
    
    public function openDetail(int $purchaseId): void
    {
        $purchase = Purchase::query()
            ->with(['supplier', 'creator', 'items.product'])
            ->findOrFail($purchaseId);

        $this->detailPurchase = [
            'invoice_number' => $purchase->invoice_number,
            'supplier' => $purchase->supplier?->name ?? 'Tanpa Supplier',
            'purchase_date' => $purchase->purchase_date->format('d M Y'),
            'creator' => $purchase->creator->name,
            'total_amount' => (float) $purchase->total_amount,
            'note' => $purchase->note,
            'items' => $purchase->items->map(function ($item) {
                return [
                    'product_name' => $item->product->name,
                    'sku' => $item->product->sku,
                    'quantity' => $item->quantity,
                    'unit_cost' => (float) $item->unit_cost,
                    'subtotal' => (float) $item->subtotal,
                ];
            })->toArray(),
        ];

        $this->showDetailModal = true;
    }

    public function savePurchase(): void
    {
        $this->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
        ], [
            'purchase_date.required' => 'Tanggal pembelian wajib diisi.',
            'items.required' => 'Minimal satu produk harus ditambahkan.',
            'items.min' => 'Minimal satu produk harus ditambahkan.',
        ]);

        DB::beginTransaction();

        try {
            $purchase = Purchase::query()->create([
                'supplier_id' => $this->supplier_id,
                'invoice_number' => $this->generatePurchaseInvoiceNumber(),
                'purchase_date' => $this->purchase_date,
                'total_amount' => $this->totalAmount(),
                'note' => $this->note,
                'created_by' => Auth::id(),
            ]);

            foreach ($this->items as $item) {
                $product = Product::query()
                    ->lockForUpdate()
                    ->findOrFail($item['product_id']);

                $quantity = (int) $item['quantity'];
                $unitCost = (float) $item['unit_cost'];
                $subtotal = $quantity * $unitCost;

                $stockBefore = (int) $product->stock;
                $averageCostBefore = (float) $product->average_cost;

                $oldStockValue = $stockBefore * $averageCostBefore;
                $incomingStockValue = $quantity * $unitCost;

                $stockAfter = $stockBefore + $quantity;

                $averageCostAfter = $stockAfter > 0
                    ? ($oldStockValue + $incomingStockValue) / $stockAfter
                    : $unitCost;

                PurchaseItem::query()->create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'subtotal' => $subtotal,
                ]);

                $product->update([
                    'stock' => $stockAfter,
                    'purchase_price' => $unitCost,
                    'average_cost' => $averageCostAfter,
                ]);

                StockMovement::query()->create([
                    'product_id' => $product->id,
                    'type' => 'purchase',
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'quantity_in' => $quantity,
                    'quantity_out' => 0,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'average_cost_before' => $averageCostBefore,
                    'average_cost_after' => $averageCostAfter,
                    'note' => 'Pembelian barang dari supplier.',
                    'created_by' => Auth::id(),
                ]);
            }

            DB::commit();

            Session::flash(
                'success',
                "Pembelian berhasil disimpan. Invoice: {$purchase->invoice_number}"
            );

            $this->showModal = false;

            $this->resetForm();

            $this->dispatch('$refresh');
        } catch (\Throwable $e) {
            DB::rollBack();

            Session::flash('error', $e->getMessage());
        }
    }

    public function totalAmount(): float
    {
        return collect($this->items)->sum('subtotal');
    }

    private function currencyToNumber(?string $value): int
    {
        return (int) preg_replace('/\D/', '', (string) $value);
    }

    private function generatePurchaseInvoiceNumber(): string
    {
        $date = now()->format('Ymd');

        $lastPurchase = Purchase::query()
            ->whereDate('created_at', '=', now()->toDateString())
            ->latest('id')
            ->first();

        $lastNumber = 0;

        if ($lastPurchase) {
            $parts = explode('-', $lastPurchase->invoice_number);

            $lastNumber = (int) end($parts);
        }

        $newNumber = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);

        return "PUR-{$date}-{$newNumber}";
    }

    private function resetForm(): void
    {
        $this->reset([
            'supplier_id',
            'supplierSearch',
            'selectedSupplier',
            'note',
            'selectedProductId',
            'selectedProduct',
            'productSearch',
            'unit_cost',
            'items',
    ]);

        $this->purchase_date = now()->format('Y-m-d');
        $this->quantity = 1;

        $this->resetValidation();
    }
}
