<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        $redirectTo = route('dashboard', absolute: false);

        if (! $user->can('view dashboard')) {
            if ($user->can('view orders')) {
                $redirectTo = route('orders.index', absolute: false);
            } elseif ($user->can('view leads')) {
                $redirectTo = route('leads.index', absolute: false);
            } elseif ($user->can('view products')) {
                $redirectTo = route('products.index', absolute: false);
            } elseif ($user->can('view campaigns')) {
                $redirectTo = route('campaigns.index', absolute: false);
            } elseif ($user->can('manage users')) {
                $redirectTo = route('users.index', absolute: false);
            } elseif ($user->can('view permissions')) {
                $redirectTo = route('roles.index', absolute: false);
            }
        }

        return redirect()->intended($redirectTo);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
