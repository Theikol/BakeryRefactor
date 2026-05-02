<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Show register page
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle register request
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. VALIDASI INPUT
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'string', 'email', 'max:100', 'unique:admins,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. DEBUG (hapus kalau sudah normal)
        // dd($validated);

        // 3. CREATE ADMIN
        $admin = Admin::create([
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'password_hash' => Hash::make($validated['password']),
        ]);

        // 4. EVENT REGISTERED
        event(new Registered($admin));

        // 5. LOGIN ADMIN
        Auth::login($admin);

        // 6. REDIRECT
        return redirect()->route('dashboard');
    }
}