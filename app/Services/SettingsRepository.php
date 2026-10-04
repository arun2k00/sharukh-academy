<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Editable platform settings, cached so the layout does not query on every request.
 */
class SettingsRepository
{
    private const CACHE_KEY = 'academy.settings';

    /**
     * @return array<string, string|null>
     */
    public function all(): array
    {
        return Cache::rememberForever(
            self::CACHE_KEY,
            fn (): array => Setting::query()->pluck('value', 'key')->all(),
        );
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $value = $this->all()[$key] ?? null;

        return filled($value) ? $value : $default;
    }

    /**
     * The public URL for a setting that stores a file path (e.g. the logo or
     * favicon), or null if it has not been set.
     */
    public function imageUrl(string $key): ?string
    {
        $path = $this->get($key);

        return $path !== null ? Storage::disk('public')->url($path) : null;
    }

    public function set(string $key, ?string $value, string $group = 'general'): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);

        $this->flush();
    }

    /**
     * @param  array<string, string|null>  $values
     */
    public function setMany(array $values, string $group = 'general'): void
    {
        foreach ($values as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        }

        $this->flush();
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
