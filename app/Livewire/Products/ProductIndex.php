<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Unit;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class ProductIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search = '';

    public bool $showModal = false;

    public ?int $productId = null;

    public string $name = '';
    public string $sku = '';
    public ?string $barcode = null;
    public ?string $description = null;

    public int|string|null $category_id = null;
    public int|string|null $unit_id = null;

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
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
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
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
            'unit_id.required' => 'Satuan wajib dipilih.',
            'unit_id.exists' => 'Satuan tidak valid.',
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

    public function render()
    {
        return view('livewire.products.product-index', [
            'products' => Product::query()
                ->with(['category', 'unit'])
                ->where(function ($query): void {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%')
                        ->orWhere('barcode', 'like', '%' . $this->search . '%');
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
        $product = Product::query()->findOrFail($id);

        $this->resetValidation();

        $this->productId = $product->id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->barcode = $product->barcode;
        $this->description = $product->description;
        $this->category_id = $product->category_id;
        $this->unit_id = $product->unit_id;
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

        if ($this->productId) {
            Product::query()
                ->findOrFail($this->productId)
                ->update([
                    'category_id' => (int) $validated['category_id'],
                    'unit_id' => (int) $validated['unit_id'],
                    'name' => $validated['name'],
                    'slug' => Str::slug($validated['name']),
                    'sku' => $validated['sku'],
                    'barcode' => $validated['barcode'] ?? null,
                    'description' => $this->description,
                    'purchase_price' => (int) $validated['purchase_price'],
                    'selling_price' => (int) $validated['selling_price'],

                    // average_cost tidak ikut diubah saat edit produk,
                    // karena nilai ini sudah dipengaruhi pembelian dan moving average.

                    'minimum_stock' => (int) $validated['minimum_stock'],
                    'is_active' => $this->is_active,
                ]);
        } else {
            Product::query()->create([
                'category_id' => (int) $validated['category_id'],
                'unit_id' => (int) $validated['unit_id'],
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'sku' => $validated['sku'],
                'barcode' => $validated['barcode'] ?? null,
                'description' => $this->description,
                'purchase_price' => (int) $validated['purchase_price'],
                'selling_price' => (int) $validated['selling_price'],
                'average_cost' => (int) $validated['purchase_price'],
                'stock' => (int) $validated['stock'],
                'minimum_stock' => (int) $validated['minimum_stock'],
                'is_active' => $this->is_active,
            ]);
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
        $this->validate([
            'bulkPrices' => ['required', 'array', 'min:1'],
            'bulkPrices.*.min_qty' => ['required', 'integer', 'min:1'],
            'bulkPrices.*.max_qty' => ['nullable', 'integer', 'min:1'],
            'bulkPrices.*.price' => ['required', 'integer', 'min:0'],
        ]);

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

        Session::flash('success', 'Harga grosir berhasil diperbarui.');

        $this->showBulkPriceModal = false;
        $this->bulkProductId = null;
        $this->bulkProductName = '';
        $this->bulkPrices = [];

        $this->dispatch('$refresh');
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
            'unit_id',
            'purchase_price',
            'selling_price',
            'stock',
            'minimum_stock',
            'is_active',
        ]);

        $this->category_id = null;
        $this->unit_id = null;
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

    public function updatedBarcode(): void
    {
        $this->barcode = preg_replace('/[^A-Za-z0-9]/', '', (string) $this->barcode);
    }
}
