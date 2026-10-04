<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Support\SafeRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(Request $request): View
    {
        return view('auth.login', [
            'redirectTo' => SafeRedirect::pathOrNull($request->query('redirect')),
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        if (! Auth::attempt($request->credentials(), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Those details do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        $user->forceFill(['last_login_at' => now()])->saveQuietly();

        // A same-site redirect target from a deep link (e.g. "Login to join"
        // on a shared live-class page) takes priority over the usual
        // role-based landing page.
        $redirectTo = SafeRedirect::pathOrNull($request->input('redirect'));

        if ($redirectTo !== null) {
            return redirect($redirectTo);
        }

        return redirect()->intended(match (true) {
            $user->isAdmin() => route('admin.dashboard'),
            $user->isTrainer() => route('tutor.dashboard'),
            default => route('dashboard'),
        });
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
