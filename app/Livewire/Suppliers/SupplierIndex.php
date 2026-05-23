<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $supplierId = null;

    public string $name = '';

    public ?string $phone = null;

    public ?string $address = null;

    public ?string $note = null;

    public bool $is_active = true;

    protected string $paginationTheme = 'tailwind';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'regex:/^[0-9]+$/', 'digits_between:8,15'],
            'address' => ['nullable', 'string', 'max:1000'],
            'note' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama supplier wajib diisi.',
            'phone.regex' => 'Nomor HP hanya boleh berisi angka.',
            'phone.digits_between' => 'Nomor HP harus terdiri dari 8 sampai 15 digit.',
            'address.max' => 'Alamat maksimal 1000 karakter.',
            'note.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }

    public function render()
    {
        return view('livewire.suppliers.supplier-index', [
            'suppliers' => Supplier::query()
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->paginate(10),
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // public function updatedPhone(): void
    // {
    //     $this->phone = preg_replace('/\D/', '', (string) $this->phone);
    // }

    public function create(): void
    {
        $this->resetForm();

        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $supplier = Supplier::query()->findOrFail($id);

        $this->supplierId = $supplier->id;
        $this->name = $supplier->name;
        $this->phone = $supplier->phone;
        $this->address = $supplier->address;
        $this->note = $supplier->note;
        $this->is_active = $supplier->is_active;

        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        Supplier::query()->updateOrCreate(
            ['id' => $this->supplierId],
            $validated
        );

        Session::flash(
            'success',
            $this->supplierId
                ? 'Data supplier berhasil diperbarui.'
                : 'Data supplier berhasil ditambahkan.'
        );

        $this->showModal = false;

        $this->resetForm();

        $this->dispatch('$refresh');
    }

    public function toggleStatus(int $id): void
    {
        /** @var Supplier $supplier */
        $supplier = Supplier::query()->findOrFail($id);

        $supplier->update([
            'is_active' => ! $supplier->is_active,
        ]);

        Session::flash('success', 'Status supplier berhasil diperbarui.');
    }

    private function resetForm(): void
    {
        $this->reset([
            'supplierId',
            'name',
            'phone',
            'address',
            'note',
            'is_active',
        ]);

        $this->is_active = true;

        $this->resetValidation();
    }
}
