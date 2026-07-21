<x-guest-layout>
    <div x-data="{
        tab: '{{ ($activeTab ?? null) === 'register' || old('name') || old('phone') || old('address') || $errors->has('name') || $errors->has('customer_type') || $errors->has('phone') || $errors->has('address') || $errors->has('password_confirmation') ? 'register' : 'login' }}'
    }" id="auth-tab-container">
        <!-- Tab Toggle -->
        <div class="clay-tabs">
            <button type="button"
                class="clay-tab"
                :class="{ 'active': tab === 'login' }"
                @click="tab = 'login'"
                onclick="if(window.Alpine) { window.Alpine.$data(this.closest('#auth-tab-container')).tab = 'login'; }"
                id="tab-login">
                ✨ Masuk
            </button>
            <button type="button"
                class="clay-tab"
                :class="{ 'active': tab === 'register' }"
                @click="tab = 'register'"
                onclick="if(window.Alpine) { window.Alpine.$data(this.closest('#auth-tab-container')).tab = 'register'; }"
                id="tab-register">
                🚀 Daftar
            </button>
        </div>

        <!-- ════════════════════════════════════ -->
        <!-- LOGIN FORM                           -->
        <!-- ════════════════════════════════════ -->
        <div x-show="tab === 'login'"
             x-transition:enter="form-panel"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="form-panel">

            <p class="form-subtitle">
                Masuk ke akun Anda dan mulai pesan minuman grosir sekarang 🥤
            </p>

            <!-- Session Status -->
            @if (session('status'))
                <div class="clay-status">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="login-form">
                @csrf

                <!-- Email Address -->
                <div class="clay-field">
                    <label for="login-email">Email</label>
                    <input id="login-email"
                           class="clay-input"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="username"
                           placeholder="nama@email.com">
                    @if ($errors->has('email') && ($activeTab ?? 'login') === 'login')
                        <ul class="clay-error">
                            @foreach ($errors->get('email') as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Password -->
                <div class="clay-field">
                    <label for="login-password">Password</label>
                    <input id="login-password"
                           class="clay-input"
                           type="password"
                           name="password"
                           required
                           autocomplete="current-password"
                           placeholder="••••••••">
                    @if ($errors->has('password') && ($activeTab ?? 'login') === 'login')
                        <ul class="clay-error">
                            @foreach ($errors->get('password') as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Remember Me + Forgot Password -->
                <div class="form-actions">
                    <div class="clay-checkbox-group">
                        <input id="remember_me" type="checkbox" name="remember">
                        <label for="remember_me">Ingat saya</label>
                    </div>

                    @if (Route::has('password.request'))
                        <a class="clay-link" href="{{ route('password.request') }}">
                            Lupa password?
                        </a>
                    @endif
                </div>

                <!-- Submit -->
                <button type="submit" class="clay-btn" id="login-submit">
                    Masuk Sekarang →
                </button>
            </form>
        </div>

        <!-- ════════════════════════════════════ -->
        <!-- REGISTER FORM                        -->
        <!-- ════════════════════════════════════ -->
        <div x-show="tab === 'register'"
             x-transition:enter="form-panel"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-cloak
             class="form-panel">

            <p class="form-subtitle">
                Buat akun dan nikmati kemudahan belanja minuman grosir online 🎉
            </p>

            <form method="POST" action="{{ route('register') }}" id="register-form">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-0">
                    <!-- Name -->
                    <div class="clay-field">
                        <label for="reg-name">Nama Lengkap</label>
                        <input id="reg-name"
                               class="clay-input"
                               type="text"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               autocomplete="name"
                               placeholder="Nama lengkap Anda">
                        @if ($errors->has('name'))
                            <ul class="clay-error">
                                @foreach ($errors->get('name') as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <!-- Email Address -->
                    <div class="clay-field">
                        <label for="reg-email">Email</label>
                        <input id="reg-email"
                               class="clay-input"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autocomplete="username"
                               placeholder="nama@email.com">
                        @if ($errors->has('email') && ($activeTab ?? 'login') === 'register')
                            <ul class="clay-error">
                                @foreach ($errors->get('email') as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <!-- Customer Type -->
                    <div class="clay-field">
                        <label for="customer_type">Tipe Pelanggan</label>
                        <select id="customer_type" name="customer_type" class="clay-input">
                            <option value="personal" @selected(old('customer_type', 'personal') === 'personal')>🧑 Perorangan</option>
                            <option value="store" @selected(old('customer_type') === 'store')>🏪 Toko</option>
                        </select>
                        @if ($errors->has('customer_type'))
                            <ul class="clay-error">
                                @foreach ($errors->get('customer_type') as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <!-- Phone -->
                    <div class="clay-field">
                        <label for="phone">Nomor HP</label>
                        <input id="phone"
                               class="clay-input"
                               type="text"
                               name="phone"
                               value="{{ old('phone') }}"
                               inputmode="numeric"
                               autocomplete="tel"
                               placeholder="08xxxxxxxxxx">
                        @if ($errors->has('phone'))
                            <ul class="clay-error">
                                @foreach ($errors->get('phone') as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <!-- Address (Full Width) -->
                    <div class="clay-field sm:col-span-2">
                        <label for="address">Alamat</label>
                        <textarea id="address"
                                  name="address"
                                  rows="2"
                                  class="clay-input"
                                  placeholder="Alamat lengkap Anda">{{ old('address') }}</textarea>
                        @if ($errors->has('address'))
                            <ul class="clay-error">
                                @foreach ($errors->get('address') as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <!-- Password -->
                    <div class="clay-field">
                        <label for="reg-password">Password</label>
                        <input id="reg-password"
                               class="clay-input"
                               type="password"
                               name="password"
                               required
                               autocomplete="new-password"
                               placeholder="Minimal 8 karakter">
                        @if ($errors->has('password') && ($activeTab ?? 'login') === 'register')
                            <ul class="clay-error">
                                @foreach ($errors->get('password') as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <!-- Confirm Password -->
                    <div class="clay-field">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input id="password_confirmation"
                               class="clay-input"
                               type="password"
                               name="password_confirmation"
                               required
                               autocomplete="new-password"
                               placeholder="Ulangi password">
                        @if ($errors->has('password_confirmation'))
                            <ul class="clay-error">
                                @foreach ($errors->get('password_confirmation') as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <!-- Submit (Full Width) -->
                    <div class="sm:col-span-2">
                        <button type="submit" class="clay-btn" id="register-submit">
                            Daftar Sekarang →
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-guest-layout>
