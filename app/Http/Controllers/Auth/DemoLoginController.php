<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * One-click sign-in into a seeded demo account, so a visitor evaluating the
 * academy can see the Trainer or Student experience without knowing test
 * credentials. No admin option — that account is never offered publicly.
 *
 * The session is marked read-only (see EnsureDemoAccountIsReadOnly): these
 * are real, shared accounts, and one of them owns real course content, so a
 * visitor may look around but never save a change.
 *
 * Disabled outside local/demo environments — see config('academy.demo_login_enabled').
 */
class DemoLoginController extends Controller
{
    /**
     * @var array<string, string>
     */
    private const ACCOUNTS = [
        'trainer' => 'trainer@aixone.site',
        'student' => 'student@aixone.site',
    ];

    public function login(Request $request, string $role): RedirectResponse
    {
        abort_unless(config('academy.demo_login_enabled'), 404);

        $email = self::ACCOUNTS[$role] ?? abort(404);

        $user = User::where('email', $email)->firstOrFail();

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('is_demo_session', true);

        $user->forceFill(['last_login_at' => now()])->saveQuietly();

        return redirect(match (true) {
            $user->isTrainer() => route('tutor.dashboard'),
            default => route('dashboard'),
        });
    }
}
