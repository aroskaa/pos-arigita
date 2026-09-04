<?php

namespace App\Livewire\Promos;

use App\Models\Product;
use App\Models\Promo;
use App\Models\Setting;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\WithPagination;

class PromoIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search = '';

    public bool $showModal = false;
    public ?int $promoId = null;

    public string $name = '';
    public string $type = 'percent';
    public int|string|null $value = null;
    public string $starts_at = '';
    public string $ends_at = '';
    public bool $is_active = true;
    public array $productIds = [];
    public string $productSearch = '';

    public int|string|null $minOrderTotal = 0;
    public int|string|null $minOrderQty = 0;

    public int|string|null $minMarginRp = 500;
    public int|string|null $minMarginPct = 2;

    public function mount(): void
    {
        $this->minOrderTotal = (int) Setting::get('min_order_total', 0);
        $this->minOrderQty = (int) Setting::get('min_order_qty', 0);
        $this->minMarginRp = (int) Setting::get('min_margin_rp', 500);
        $this->minMarginPct = (float) Setting::get('min_margin_pct', 2);
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:percent,fixed'],
            'value' => [
                'required',
                'numeric',
                'min:1',
                'max:' . ($this->type === 'percent' ? 100 : 9999999999),
            ],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'productIds' => ['required', 'array', 'min:1'],
            'productIds.*' => ['integer', 'exists:products,id'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama promo wajib diisi.',
            'type.required' => 'Tipe diskon wajib dipilih.',
            'type.in' => 'Tipe diskon tidak valid.',
            'value.required' => 'Nilai diskon wajib diisi.',
            'value.numeric' => 'Nilai diskon harus berupa angka.',
            'value.min' => 'Nilai diskon minimal 1.',
            'value.max' => 'Diskon persen tidak boleh lebih dari 100.',
            'starts_at.required' => 'Tanggal mulai wajib diisi.',
            'starts_at.date' => 'Tanggal mulai tidak valid.',
            'ends_at.date' => 'Tanggal selesai tidak valid.',
            'ends_at.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'productIds.required' => 'Minimal satu produk wajib dipilih.',
            'productIds.min' => 'Minimal satu produk wajib dipilih.',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.promos.promo-index', [
            'promos' => Promo::query()
                ->with('products')
                ->when($this->search !== '', function ($query): void {
                    $query->where('name', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->paginate(10),

            'productOptions' => Product::query()
                ->where('is_active', true)
                ->with('unit')
                ->when($this->productSearch !== '', function ($query): void {
                    $query->where(function ($q): void {
                        $q->where('name', 'like', '%' . $this->productSearch . '%')
                            ->orWhere('sku', 'like', '%' . $this->productSearch . '%');
                    });
                })
                ->orderBy('name')
                ->limit(100)
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
        /** @var Promo $promo */
        $promo = Promo::query()->with('products')->findOrFail($id);

        $this->resetValidation();

        $this->promoId = $promo->id;
        $this->name = $promo->name;
        $this->type = $promo->type;
        $this->value = $promo->type === 'percent'
            ? (float) $promo->value
            : (int) $promo->value;
        $this->starts_at = $promo->starts_at?->format('Y-m-d\TH:i') ?? '';
        $this->ends_at = $promo->ends_at?->format('Y-m-d\TH:i') ?? '';
        $this->is_active = (bool) $promo->is_active;
        $this->productIds = $promo->products->pluck('id')->map(fn ($id) => (int) $id)->all();

        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        $payload = [
            'name' => $validated['name'],
            'type' => $validated['type'],
            'value' => (float) $validated['value'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'] ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->promoId) {
            /** @var Promo $promo */
            $promo = Promo::query()->findOrFail($this->promoId);
            $promo->update($payload);
            $promo->products()->sync($validated['productIds']);

            ActivityLogger::log(
                'promo.updated',
                "Promo {$promo->name} diperbarui.",
                $promo,
                [
                    'type' => $promo->type,
                    'value' => (float) $promo->value,
                    'product_count' => count($validated['productIds']),
                    'is_active' => (bool) $promo->is_active,
                ],
            );

            Session::flash('success', 'Promo berhasil diperbarui.');
        } else {
            $promo = Promo::query()->create($payload);
            $promo->products()->sync($validated['productIds']);

            ActivityLogger::log(
                'promo.created',
                "Promo {$promo->name} ditambahkan.",
                $promo,
                [
                    'type' => $promo->type,
                    'value' => (float) $promo->value,
                    'product_count' => count($validated['productIds']),
                ],
            );

            Session::flash('success', 'Promo berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->resetForm();
        $this->resetPage();
        $this->dispatch('$refresh');
    }

    public function toggleActive(int $id): void
    {
        /** @var Promo $promo */
        $promo = Promo::query()->findOrFail($id);
        $promo->update(['is_active' => ! $promo->is_active]);

        ActivityLogger::log(
            'promo.toggled',
            $promo->is_active
                ? "Promo {$promo->name} diaktifkan."
                : "Promo {$promo->name} dinonaktifkan.",
            $promo,
            ['is_active' => (bool) $promo->is_active],
        );

        Session::flash('success', $promo->is_active ? 'Promo diaktifkan.' : 'Promo dinonaktifkan.');
    }

    public function delete(int $id): void
    {
        /** @var Promo $promo */
        $promo = Promo::query()->findOrFail($id);

        ActivityLogger::log(
            'promo.deleted',
            "Promo {$promo->name} dihapus.",
            $promo,
            ['type' => $promo->type, 'value' => (float) $promo->value],
        );

        $promo->products()->detach();
        $promo->delete();

        Session::flash('success', 'Promo berhasil dihapus.');
    }

    public function saveSettings(): void
    {
        $this->validate([
            'minOrderTotal' => ['required', 'integer', 'min:0'],
            'minOrderQty' => ['required', 'integer', 'min:0'],
            'minMarginRp' => ['required', 'integer', 'min:0'],
            'minMarginPct' => ['required', 'numeric', 'min:0', 'max:100'],
        ], [
            'minOrderTotal.required' => 'Minimal total order wajib diisi.',
            'minOrderTotal.integer' => 'Minimal total order harus berupa angka.',
            'minOrderTotal.min' => 'Minimal total order tidak boleh negatif.',
            'minOrderQty.required' => 'Minimal jumlah unit wajib diisi.',
            'minOrderQty.integer' => 'Minimal jumlah unit harus berupa angka.',
            'minOrderQty.min' => 'Minimal jumlah unit tidak boleh negatif.',
            'minMarginRp.required' => 'Margin minimum rupiah wajib diisi.',
            'minMarginRp.integer' => 'Margin minimum rupiah harus berupa angka.',
            'minMarginRp.min' => 'Margin minimum rupiah tidak boleh negatif.',
            'minMarginPct.required' => 'Margin minimum persen wajib diisi.',
            'minMarginPct.numeric' => 'Margin minimum persen harus berupa angka.',
            'minMarginPct.min' => 'Margin minimum persen tidak boleh negatif.',
            'minMarginPct.max' => 'Margin minimum persen tidak boleh lebih dari 100.',
        ]);

        Setting::set('min_order_total', (int) $this->minOrderTotal);
        Setting::set('min_order_qty', (int) $this->minOrderQty);
        Setting::set('min_margin_rp', (int) $this->minMarginRp);
        Setting::set('min_margin_pct', (float) $this->minMarginPct);

        ActivityLogger::log(
            'settings.updated',
            'Pengaturan minimum order & margin diperbarui.',
            null,
            [
                'min_order_total' => (int) $this->minOrderTotal,
                'min_order_qty' => (int) $this->minOrderQty,
                'min_margin_rp' => (int) $this->minMarginRp,
                'min_margin_pct' => (float) $this->minMarginPct,
            ],
        );

        Session::flash('success', 'Pengaturan berhasil disimpan.');
    }

    private function resetForm(): void
    {
        $this->reset([
            'promoId',
            'name',
            'type',
            'value',
            'starts_at',
            'ends_at',
            'is_active',
            'productIds',
            'productSearch',
        ]);

        $this->name = '';
        $this->type = 'percent';
        $this->starts_at = '';
        $this->ends_at = '';
        $this->is_active = true;
        $this->productIds = [];
    }
}
