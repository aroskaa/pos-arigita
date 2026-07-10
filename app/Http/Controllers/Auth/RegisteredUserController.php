<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.login', ['activeTab' => 'register']);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'regex:/^[0-9]+$/', 'digits_between:8,15'],
            'customer_type' => ['nullable', 'in:personal,store'],
            'address' => ['nullable', 'string', 'max:1000'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'phone.regex' => 'Nomor HP hanya boleh berisi angka.',
            'phone.digits_between' => 'Nomor HP harus terdiri dari 8 sampai 15 digit.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'customer',
            'password' => Hash::make($request->password),
        ]);

        Customer::query()->create([
            'user_id' => $user->id,
            'type' => $request->customer_type ?: 'personal',
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'note' => null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
