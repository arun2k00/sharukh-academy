<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingsRequest;
use App\Services\SettingsRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        private readonly SettingsRepository $settings,
    ) {}

    public function edit(): View
    {
        return view('admin.settings', [
            'settings' => $this->settings->all(),
        ]);
    }

    public function update(SettingsRequest $request): RedirectResponse
    {
        $this->settings->setMany($request->validated());

        return back()->with('status', 'Settings saved.');
    }
}
