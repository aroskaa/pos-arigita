<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Unit;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class ProductIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search = '';
    public string $selectedCategory = '';

    public bool $showModal = false;

    public ?int $productId = null;

    public string $name = '';
    public string $sku = '';
    public ?string $barcode = null;
    public ?string $description = null;

    public int|string|null $category_id = null;
    public string $categoryInput = '';

    public int|string|null $unit_id = null;
    public string $unitInput = '';

    public ?int $purchase_price = null;
    public ?int $selling_price = null;

    public int|string|null $stock = null;
    public int|string|null $minimum_stock = null;

    public bool $is_active = true;

    public bool $showBulkPriceModal = false;
    public ?int $bulkProductId = null;
    public string $bulkProductName = '';
    public array $bulkPrices = [];

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255'],
            'barcode' => ['nullable', 'string', 'max:255'],
            'categoryInput' => ['required', 'string', 'max:255'],
            'unitInput' => ['required', 'string', 'max:255'],
            'purchase_price' => ['required', 'integer', 'min:0'],
            'selling_price' => ['required', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama produk wajib diisi.',
            'sku.required' => 'SKU wajib diisi.',
            'categoryInput.required' => 'Kategori wajib diisi.',
            'unitInput.required' => 'Satuan wajib diisi.',
            'purchase_price.required' => 'Harga beli wajib diisi.',
            'purchase_price.integer' => 'Harga beli harus berupa angka.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'selling_price.integer' => 'Harga jual harus berupa angka.',
            'stock.required' => 'Stok wajib diisi.',
            'stock.integer' => 'Stok harus berupa angka.',
            'minimum_stock.required' => 'Minimum stok wajib diisi.',
            'minimum_stock.integer' => 'Minimum stok harus berupa angka.',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.products.product-index', [
            'products' => Product::query()
                ->with(['category', 'unit', 'prices'])
                ->when($this->search !== '', function ($query): void {
                    $query->where(function ($q): void {
                        $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('sku', 'like', '%' . $this->search . '%')
                            ->orWhere('barcode', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->selectedCategory !== '', function ($query): void {
                    $query->where('category_id', '=', $this->selectedCategory);
                })
                ->latest()
                ->paginate(10),

            'categories' => Category::query()
                ->where('is_active', '=', true)
                ->orderBy('name', 'asc')
                ->get(),

            'units' => Unit::query()
                ->orderBy('name', 'asc')
                ->get(),
        ]);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->resetValidation();

        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        /** @var Product $product */
        $product = Product::query()->with(['category', 'unit', 'prices'])->findOrFail($id);

        $this->resetValidation();

        $this->productId = $product->id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->barcode = $product->barcode;
        $this->description = $product->description;
        $this->category_id = $product->category_id;
        $this->categoryInput = $product->category?->name ?? '';
        $this->unit_id = $product->unit_id;
        $this->unitInput = $product->unit?->name ?? '';
        $this->purchase_price = (int) $product->purchase_price;
        $this->selling_price = (int) $product->selling_price;
        $this->stock = $product->stock;
        $this->minimum_stock = $product->minimum_stock;
        $this->is_active = (bool) $product->is_active;

        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        $categoryName = trim((string) $validated['categoryInput']);
        $category = Category::query()
            ->whereRaw('LOWER(name) = ?', [strtolower($categoryName)])
            ->first();

        if (! $category) {
            $category = Category::query()->create([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
                'is_active' => true,
            ]);
        }

        $unitName = trim((string) $validated['unitInput']);
        $unit = Unit::query()
            ->whereRaw('LOWER(name) = ?', [strtolower($unitName)])
            ->first();

        if (! $unit) {
            $unit = Unit::query()->create([
                'name' => $unitName,
                'abbreviation' => strtoupper(substr($unitName, 0, 4)),
            ]);
        }

        if ($this->productId) {
            $product = Product::query()->findOrFail($this->productId);

            $product->update([
                'category_id' => $category->id,
                'unit_id' => $unit->id,
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'sku' => $validated['sku'],
                'barcode' => $validated['barcode'] ?? null,
                'description' => $this->description,
                'purchase_price' => (int) $validated['purchase_price'],

                // average_cost tidak ikut diubah saat edit produk,
                // karena nilai ini sudah dipengaruhi pembelian dan moving average.

                'minimum_stock' => (int) $validated['minimum_stock'],
                'is_active' => $this->is_active,
            ]);

            $this->updateTierOnePrice($product->id, (int) $validated['selling_price']);

            ActivityLogger::log(
                'product.updated',
                "Produk {$product->name} diperbarui.",
                $product,
                [
                    'sku' => $product->sku,
                    'selling_price' => (float) $product->selling_price,
                    'minimum_stock' => (int) $product->minimum_stock,
                    'is_active' => (bool) $product->is_active,
                ],
            );
        } else {
            $product = Product::query()->create([
                'category_id' => $category->id,
                'unit_id' => $unit->id,
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'sku' => $validated['sku'],
                'barcode' => $validated['barcode'] ?? null,
                'description' => $this->description,
                'purchase_price' => (int) $validated['purchase_price'],
                'average_cost' => (int) $validated['purchase_price'],
                'stock' => (int) $validated['stock'],
                'minimum_stock' => (int) $validated['minimum_stock'],
                'is_active' => $this->is_active,
            ]);

            $this->updateTierOnePrice($product->id, (int) $validated['selling_price']);

            ActivityLogger::log(
                'product.created',
                "Produk {$product->name} ditambahkan.",
                $product,
                [
                    'sku' => $product->sku,
                    'stock' => (int) $product->stock,
                    'selling_price' => (float) $product->selling_price,
                ],
            );
        }

        Session::flash(
            'success',
            $this->productId
                ? 'Produk berhasil diperbarui.'
                : 'Produk berhasil ditambahkan.'
        );

        $this->showModal = false;
        $this->resetForm();
        $this->resetPage();
        $this->dispatch('$refresh');
    }

    public function delete(int $id): void
    {
        /** @var Product $product */
        $product = Product::query()->findOrFail($id);

        ActivityLogger::log(
            'product.deleted',
            "Produk {$product->name} dihapus.",
            $product,
            [
                'sku' => $product->sku,
                'stock' => (int) $product->stock,
            ],
        );

        $product->delete();

        Session::flash('success', 'Produk berhasil dihapus.');
    }

    public function openBulkPrices(int $id): void
    {
        $product = Product::query()
            ->with('prices')
            ->findOrFail($id);

        $this->bulkProductId = $product->id;
        $this->bulkProductName = $product->name;

        $this->bulkPrices = $product->prices
            ->sortBy('min_qty')
            ->map(function ($price) {
                return [
                    'id' => $price->id,
                    'min_qty' => $price->min_qty,
                    'max_qty' => $price->max_qty,
                    'price' => (int) $price->price,
                ];
            })
            ->values()
            ->toArray();

        if (count($this->bulkPrices) === 0) {
            $this->addBulkPriceRow();
        }

        $this->showBulkPriceModal = true;
    }

    public function addBulkPriceRow(): void
    {
        $this->bulkPrices[] = [
            'id' => null,
            'min_qty' => null,
            'max_qty' => null,
            'price' => null,
        ];
    }

    public function removeBulkPriceRow(int $index): void
    {
        unset($this->bulkPrices[$index]);

        $this->bulkPrices = array_values($this->bulkPrices);
    }

    public function saveBulkPrices(): void
    {
        $this->resetErrorBag();

        $this->validate([
            'bulkProductId' => ['required', 'exists:products,id'],
            'bulkPrices' => ['required', 'array', 'min:1'],
            'bulkPrices.*.min_qty' => ['required', 'integer', 'min:1'],
            'bulkPrices.*.max_qty' => ['nullable', 'integer', 'min:1'],
            'bulkPrices.*.price' => ['required', 'integer', 'min:0'],
        ], [
            'bulkProductId.required' => 'Produk tidak ditemukan. Tutup modal dan buka ulang harga grosir.',
            'bulkPrices.required' => 'Minimal satu baris harga grosir wajib diisi.',
            'bulkPrices.min' => 'Minimal satu baris harga grosir wajib diisi.',
            'bulkPrices.*.min_qty.required' => 'Minimal qty wajib diisi.',
            'bulkPrices.*.min_qty.integer' => 'Minimal qty harus berupa angka.',
            'bulkPrices.*.min_qty.min' => 'Minimal qty paling kecil adalah 1.',
            'bulkPrices.*.max_qty.integer' => 'Maksimal qty harus berupa angka.',
            'bulkPrices.*.max_qty.min' => 'Maksimal qty paling kecil adalah 1.',
            'bulkPrices.*.price.required' => 'Harga wajib diisi.',
            'bulkPrices.*.price.integer' => 'Harga harus berupa angka.',
            'bulkPrices.*.price.min' => 'Harga tidak boleh kurang dari 0.',
        ]);

        if (! $this->validateBulkPriceRanges()) {
            return;
        }

        ProductPrice::query()
            ->where('product_id', '=', $this->bulkProductId)
            ->delete();

        foreach ($this->bulkPrices as $price) {
            ProductPrice::query()->create([
                'product_id' => $this->bulkProductId,
                'min_qty' => $price['min_qty'],
                'max_qty' => $price['max_qty'] ?: null,
                'price' => $price['price'],
            ]);
        }

        $product = Product::query()->findOrFail($this->bulkProductId);

        ActivityLogger::log(
            'product.bulk_prices_updated',
            "Harga grosir {$product->name} diperbarui.",
            $product,
            [
                'price_tier_count' => count($this->bulkPrices),
            ],
        );

        Session::flash('success', 'Harga grosir berhasil diperbarui.');

        $this->showBulkPriceModal = false;
        $this->bulkProductId = null;
        $this->bulkProductName = '';
        $this->bulkPrices = [];

        $this->dispatch('$refresh');
    }

    private function validateBulkPriceRanges(): bool
    {
        $ranges = collect($this->bulkPrices)
            ->map(function (array $price, int $index) {
                $min = (int) $price['min_qty'];
                $max = filled($price['max_qty']) ? (int) $price['max_qty'] : null;

                return [
                    'index' => $index,
                    'min' => $min,
                    'max' => $max,
                    'end' => $max ?? PHP_INT_MAX,
                ];
            })
            ->sortBy('min')
            ->values();

        $seenMinimums = [];
        $openEndedCount = 0;
        $previous = null;

        foreach ($ranges as $range) {
            if (isset($seenMinimums[$range['min']])) {
                $this->addError(
                    "bulkPrices.{$range['index']}.min_qty",
                    'Minimal qty tidak boleh sama dengan baris lain.'
                );

                return false;
            }

            $seenMinimums[$range['min']] = true;

            if ($range['max'] !== null && $range['max'] < $range['min']) {
                $this->addError(
                    "bulkPrices.{$range['index']}.max_qty",
                    'Maksimal qty harus lebih besar atau sama dengan minimal qty.'
                );

                return false;
            }

            if ($range['max'] === null) {
                $openEndedCount++;

                if ($openEndedCount > 1) {
                    $this->addError('bulkPrices', 'Hanya boleh ada satu baris tanpa maksimal qty.');

                    return false;
                }
            }

            if ($previous && $range['min'] <= $previous['end']) {
                $this->addError(
                    'bulkPrices',
                    'Range harga grosir tidak boleh saling tumpang tindih.'
                );

                return false;
            }

            if ($previous && $previous['max'] === null) {
                $this->addError(
                    'bulkPrices',
                    'Baris tanpa maksimal qty harus menjadi range terakhir.'
                );

                return false;
            }

            $previous = $range;
        }

        return true;
    }

    private function resetForm(): void
    {
        $this->reset([
            'productId',
            'name',
            'sku',
            'barcode',
            'description',
            'category_id',
            'categoryInput',
            'unit_id',
            'unitInput',
            'purchase_price',
            'selling_price',
            'stock',
            'minimum_stock',
            'is_active',
        ]);

        $this->category_id = null;
        $this->categoryInput = '';
        $this->unit_id = null;
        $this->unitInput = '';
        $this->purchase_price = null;
        $this->selling_price = null;
        $this->stock = null;
        $this->minimum_stock = null;
        $this->is_active = true;
    }

    public function generateBarcode(): void
    {
        do {
            $barcode = 'AG' . now()->format('ymdHis') . random_int(100, 999);
        } while (
            Product::query()
                ->where('barcode', '=', $barcode)
                ->exists()
        );

        $this->barcode = $barcode;

        $this->dispatch('$refresh');
    }

    public function generateSku(): void
    {
        $name = trim((string) $this->name);

        if ($name !== '') {
            $words = preg_split('/\s+/', $name);
            $parts = array_filter(array_map(
                fn ($w) => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $w), 0, 3)),
                $words
            ));
            $base = 'AG-' . implode('-', $parts);
        } else {
            $base = 'AG-' . strtoupper(Str::random(6));
        }

        $candidate = $base;
        $counter = 2;

        while (
            Product::query()
                ->where('sku', '=', $candidate)
                ->when($this->productId, fn ($q) => $q->where('id', '!=', $this->productId))
                ->exists()
        ) {
            $candidate = $base . '-' . $counter;
            $counter++;
        }

        $this->sku = $candidate;

        $this->dispatch('$refresh');
    }

    public function updatedBarcode(): void
    {
        $this->barcode = preg_replace('/[^A-Za-z0-9]/', '', (string) $this->barcode);
    }

    private function updateTierOnePrice(int $productId, int $price): void
    {
        $tierOne = ProductPrice::query()
            ->where('product_id', '=', $productId)
            ->where('min_qty', '=', 1)
            ->first();

        if ($tierOne) {
            $tierOne->update(['price' => $price]);
        } else {
            $nextTier = ProductPrice::query()
                ->where('product_id', '=', $productId)
                ->where('min_qty', '>', 1)
                ->orderBy('min_qty', 'asc')
                ->first();

            $maxQty = $nextTier ? ($nextTier->min_qty - 1) : null;

            ProductPrice::query()->create([
                'product_id' => $productId,
                'min_qty' => 1,
                'max_qty' => $maxQty,
                'price' => $price,
            ]);
        }
    }
}
