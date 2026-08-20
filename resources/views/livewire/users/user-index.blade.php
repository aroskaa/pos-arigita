<div>
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h3 class="text-2xl font-bold text-slate-900">
                Akun Pengguna
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Kelola data akun pengguna, staf (Admin & Kasir), serta akun Pelanggan.
            </p>
        </div>

        <button
            type="button"
            wire:click="create"
            wire:loading.attr="disabled"
            class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
        >
            + Tambah Pengguna
        </button>
    </div>

    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            {{-- Filter Pills --}}
            <div class="flex flex-wrap items-center gap-2">
                <button
                    type="button"
                    wire:click="setRoleFilter('all')"
                    class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-semibold transition {{ $roleFilter === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    <span>Semua Role</span>
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $roleFilter === 'all' ? 'bg-slate-700 text-white' : 'bg-slate-200 text-slate-700' }}">
                        {{ $counts['all'] }}
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="setRoleFilter('owner')"
                    class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-semibold transition {{ $roleFilter === 'owner' ? 'bg-purple-600 text-white shadow-sm' : 'bg-purple-50 text-purple-700 hover:bg-purple-100' }}"
                >
                    <span>Owner</span>
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $roleFilter === 'owner' ? 'bg-purple-700 text-white' : 'bg-purple-100 text-purple-800' }}">
                        {{ $counts['owner'] }}
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="setRoleFilter('admin')"
                    class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-semibold transition {{ $roleFilter === 'admin' ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}"
                >
                    <span>Admin</span>
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $roleFilter === 'admin' ? 'bg-blue-700 text-white' : 'bg-blue-100 text-blue-800' }}">
                        {{ $counts['admin'] }}
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="setRoleFilter('cashier')"
                    class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-semibold transition {{ $roleFilter === 'cashier' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}"
                >
                    <span>Kasir</span>
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $roleFilter === 'cashier' ? 'bg-emerald-700 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $counts['cashier'] }}
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="setRoleFilter('customer')"
                    class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-semibold transition {{ $roleFilter === 'customer' ? 'bg-amber-600 text-white shadow-sm' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}"
                >
                    <span>Pelanggan</span>
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $roleFilter === 'customer' ? 'bg-amber-700 text-white' : 'bg-amber-100 text-amber-800' }}">
                        {{ $counts['customer'] }}
                    </span>
                </button>
            </div>

            {{-- Search Box --}}
            <div class="relative w-full lg:w-80">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama, email, hp..."
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 pl-11 pr-4 py-2.5 text-sm outline-none focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition"
                >
            </div>
        </div>

        @if (session()->has('success'))
            <div class="mt-4 rounded-2xl bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mt-4 rounded-2xl bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-slate-100 text-left">
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Pengguna</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Role</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Kontak</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Status</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600"></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($users as $userItem)
                        <tr wire:key="user-{{ $userItem->id }}" class="border-b border-slate-100">
                            <td class="px-4 py-4">
                                <div>
                                    <p class="font-semibold text-slate-900">
                                        {{ $userItem->name }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        {{ $userItem->email }}
                                    </p>
                                </div>
                            </td>

                            <td class="px-4 py-4 text-sm">
                                @if ($userItem->role === 'owner')
                                    <span class="rounded-full bg-purple-50 px-3 py-1 text-xs font-semibold text-purple-700">
                                        Owner
                                    </span>
                                @elseif ($userItem->role === 'admin')
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                        Admin
                                    </span>
                                @elseif ($userItem->role === 'cashier')
                                    <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                                        Kasir
                                    </span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                        Pelanggan
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $userItem->phone ?: '-' }}
                            </td>

                            <td class="px-4 py-4">
                                @if ($userItem->is_active)
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                        Aktif
                                    </span>
                                @else
                                    <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="edit({{ $userItem->id }})"
                                        wire:loading.attr="disabled"
                                        class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 disabled:opacity-50"
                                    >
                                        Edit
                                    </button>

                                    @if(auth()->id() !== $userItem->id)
                                        <button
                                            type="button"
                                            wire:click="toggleStatus({{ $userItem->id }})"
                                            wire:loading.attr="disabled"
                                            class="rounded-xl {{ $userItem->is_active ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }} px-3 py-2 text-xs font-semibold disabled:opacity-50"
                                        >
                                            {{ $userItem->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">
                                Belum ada data akun pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-2xl rounded-3xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">
                            {{ $userId ? 'Edit Akun Pengguna' : 'Tambah Akun Pengguna' }}
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Staf (Admin/Kasir) dapat mengakses sistem POS. Pelanggan dapat memesan dari web order.
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="$set('showModal', false)"
                        class="rounded-xl bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-200"
                    >
                        ✕
                    </button>
                </div>

                <div class="space-y-5 p-6 max-h-[65vh] overflow-y-auto">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Nama Lengkap
                        </label>

                        <input
                            type="text"
                            wire:model.live="name"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Email
                        </label>

                        <input
                            type="email"
                            wire:model.live="email"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Password {{ $userId ? '(Kosongkan jika tidak ingin diubah)' : '' }}
                        </label>

                        <input
                            type="password"
                            wire:model.live="password"
                            placeholder="{{ $userId ? '••••••••' : 'Minimal 8 karakter' }}"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Role
                        </label>

                        <select
                            wire:model.live="role"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                            <option value="owner">Owner</option>
                            <option value="admin">Admin</option>
                            <option value="cashier">Kasir</option>
                            <option value="customer">Pelanggan</option>
                        </select>

                        @error('role')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Nomor HP
                        </label>

                        <input
                            type="text"
                            inputmode="numeric"
                            wire:model.live.debounce.300ms="phone"
                            placeholder="08xxxxxxxxxx"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                        @error('phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if(auth()->id() !== $userId)
                        <label class="flex items-center gap-3">
                            <input
                                type="checkbox"
                                wire:model.live="is_active"
                                class="h-5 w-5 rounded border-slate-300 text-blue-600"
                            >

                            <span class="text-sm font-medium text-slate-700">
                                Akun aktif
                            </span>
                        </label>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-6 py-5">
                    <button
                        type="button"
                        wire:click="$set('showModal', false)"
                        class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        wire:click="save"
                        wire:loading.attr="disabled"
                        class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        Simpan Pengguna
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
