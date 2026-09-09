<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // The response is deliberately identical whether or not the address is
        // registered, so this form cannot be used to enumerate accounts.
        Password::sendResetLink($request->only('email'));

        return back()->with('status', 'If that address belongs to an account, a reset link is on its way.');
    }
}
