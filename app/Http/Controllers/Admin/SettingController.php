<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingsRequest;
use App\Services\ResourceStorageService;
use App\Services\SettingsRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        private readonly SettingsRepository $settings,
        private readonly ResourceStorageService $storage,
    ) {}

    public function edit(): View
    {
        return view('admin.settings', [
            'settings' => $this->settings->all(),
        ]);
    }

    public function update(SettingsRequest $request): RedirectResponse
    {
        $values = $request->safe()->except(['logo', 'favicon']);

        if ($request->hasFile('logo')) {
            $this->storage->deleteImage($this->settings->get('logo_path'));
            $values['logo_path'] = $this->storage->storeImage($request->file('logo'), 'brand');
        }

        if ($request->hasFile('favicon')) {
            $this->storage->deleteImage($this->settings->get('favicon_path'));
            $values['favicon_path'] = $this->storage->storeImage($request->file('favicon'), 'brand');
        }

        $this->settings->setMany($values);

        return back()->with('status', 'Settings saved.');
    }
}
