<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),

            // ✅ Admin approval না হওয়া পর্যন্ত inactive (migration ছাড়াই)
            'email_verified_at' => null,
        ]);

        // ❌ এটা দিলে email verify link দিয়ে user নিজেই active হয়ে যাবে, তাই বাদ
        // event(new Registered($user));

        // ❌ auto login বন্ধ
        // Auth::login($user);

        return redirect()
            ->route('login')
            ->with('status', 'Registration successful! আপনার একাউন্ট অ্যাডমিন approval দিলে তারপর লগইন করতে পারবেন।')
            ->withInput(['email' => $request->email]); // ✅ login form এ email বসে যাবে
    }
}
