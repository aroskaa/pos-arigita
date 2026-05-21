<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class ProductIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $productId = null;

    public string $name = '';
    public string $sku = '';
    public ?string $barcode = null;
    public ?string $description = null;

    public int $category_id = 0;
    public int $unit_id = 0;

    public float $purchase_price = 0;
    public float $selling_price = 0;

    public int $stock = 0;
    public int $minimum_stock = 0;

    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255'],
            'barcode' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ];
    }

    public function render()
    {
        return view('livewire.products.product-index', [
            'products' => Product::query()
                ->with(['category', 'unit'])
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%')
                        ->orWhere('barcode', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->paginate(10),

            'categories' => Category::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),

            'units' => Unit::query()
                ->orderBy('name', 'asc')
                ->get(),
        ]);
    }

    public function create(): void
    {
        $this->resetForm();

        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $product = Product::query()->findOrFail($id);

        $this->productId = $product->id;

        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->barcode = $product->barcode;
        $this->description = $product->description;

        $this->category_id = $product->category_id;
        $this->unit_id = $product->unit_id;

        $this->purchase_price = (float) $product->purchase_price;
        $this->selling_price = (float) $product->selling_price;

        $this->stock = $product->stock;
        $this->minimum_stock = $product->minimum_stock;

        $this->is_active = $product->is_active;

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        Product::query()->updateOrCreate(
            ['id' => $this->productId],
            [
                'category_id' => $this->category_id,
                'unit_id' => $this->unit_id,

                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'sku' => $this->sku,
                'barcode' => $this->barcode,
                'description' => $this->description,

                'purchase_price' => $this->purchase_price,
                'selling_price' => $this->selling_price,
                'average_cost' => $this->purchase_price,

                'stock' => $this->stock,
                'minimum_stock' => $this->minimum_stock,

                'is_active' => $this->is_active,
            ]
        );

        Session::flash(
            'success',
            $this->productId
                ? 'Produk berhasil diperbarui.'
                : 'Produk berhasil ditambahkan.'
        );

        $this->showModal = false;

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        /** @var Product $product */
        $product = Product::query()->findOrFail($id);

        $product->delete();

        Session::flash(
            'success',
            'Produk berhasil dihapus.'
        );
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

        $this->purchase_price = 0;
        $this->selling_price = 0;
        $this->stock = 0;
        $this->minimum_stock = 0;

        $this->is_active = true;
    }
}
