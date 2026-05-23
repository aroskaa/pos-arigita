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

    public ?string $purchase_date = null;

    public ?string $note = null;

    public ?int $selectedProductId = null;

    public int $quantity = 1;

    public int|float|string|null $unit_cost = null;

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

            'suppliers' => Supplier::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),

            'products' => Product::query()
                ->where('is_active', true)
                ->orderBy('name')
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
    }

    public function addItem(): void
    {
        $this->validate([
            'selectedProductId' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
        ], [
            'selectedProductId.required' => 'Produk wajib dipilih.',
            'quantity.required' => 'Jumlah pembelian wajib diisi.',
            'quantity.min' => 'Jumlah pembelian minimal 1.',
            'unit_cost.required' => 'Harga beli wajib diisi.',
            'unit_cost.numeric' => 'Harga beli harus berupa angka.',
        ]);

        $product = Product::query()->findOrFail($this->selectedProductId);

        $productId = $product->id;
        $quantity = (int) $this->quantity;
        $unitCost = (float) $this->unit_cost;

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
        $this->quantity = 1;
        $this->unit_cost = null;
    }

    public function removeItem(int $productId): void
    {
        unset($this->items[$productId]);
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
            'note',
            'selectedProductId',
            'unit_cost',
            'items',
        ]);

        $this->purchase_date = now()->format('Y-m-d');
        $this->quantity = 1;

        $this->resetValidation();
    }
}
