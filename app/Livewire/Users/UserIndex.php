<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $roleFilter = 'all';

    public bool $showModal = false;

    public ?int $userId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $role = 'cashier';

    public ?string $phone = null;

    public bool $is_active = true;

    protected string $paginationTheme = 'tailwind';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->userId),
            ],
            'password' => $this->userId ? ['nullable', 'string', 'min:8'] : ['required', 'string', 'min:8'],
            'role' => ['required', 'in:owner,admin,cashier,customer'],
            'phone' => ['nullable', 'regex:/^[0-9]+$/', 'digits_between:8,15'],
            'is_active' => ['boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'phone.regex' => 'Nomor HP hanya boleh berisi angka.',
            'phone.digits_between' => 'Nomor HP harus terdiri dari 8 sampai 15 digit.',
        ];
    }

    public function setRoleFilter(string $role): void
    {
        if (in_array($role, ['all', 'owner', 'admin', 'cashier', 'customer'], true)) {
            $this->roleFilter = $role;
            $this->resetPage();
        }
    }

    public function render()
    {
        $counts = [
            'all' => User::query()->count(),
            'owner' => User::query()->where('role', 'owner')->count(),
            'admin' => User::query()->where('role', 'admin')->count(),
            'cashier' => User::query()->where('role', 'cashier')->count(),
            'customer' => User::query()->where('role', 'customer')->count(),
        ];

        return view('livewire.users.user-index', [
            'users' => User::query()
                ->when($this->roleFilter !== 'all', function ($query) {
                    $query->where('role', $this->roleFilter);
                })
                ->when($this->search !== '', function ($query) {
                    $query->where(function ($sub) {
                        $sub->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%')
                            ->orWhere('phone', 'like', '%' . $this->search . '%');
                    });
                })
                ->latest()
                ->paginate(10),
            'counts' => $counts,
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $user = User::query()->findOrFail($id);

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->phone = $user->phone;
        $this->is_active = $user->is_active;
        $this->password = ''; // Keep password blank when editing

        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->userId) {
            $user = User::query()->findOrFail($this->userId);
            $data = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'phone' => $validated['phone'],
                'is_active' => $validated['is_active'],
            ];
            if (!empty($validated['password'])) {
                $data['password'] = Hash::make($validated['password']);
            }
            $user->update($data);
        } else {
            User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'phone' => $validated['phone'],
                'is_active' => $validated['is_active'],
                'password' => Hash::make($validated['password']),
            ]);
        }

        Session::flash(
            'success',
            $this->userId
                ? 'Akun pengguna berhasil diperbarui.'
                : 'Akun pengguna berhasil ditambahkan.'
        );

        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('$refresh');
    }

    public function toggleStatus(int $id): void
    {
        // Don't allow self-deactivation
        if (auth()->id() === $id) {
            Session::flash('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
            return;
        }

        /** @var User $user */
        $user = User::query()->findOrFail($id);
        $user->update([
            'is_active' => !$user->is_active,
        ]);

        Session::flash('success', 'Status akun pengguna berhasil diperbarui.');
    }

    private function resetForm(): void
    {
        $this->reset([
            'userId',
            'name',
            'email',
            'password',
            'role',
            'phone',
            'is_active',
        ]);
        $this->role = 'cashier';
        $this->is_active = true;
        $this->resetValidation();
    }
}
