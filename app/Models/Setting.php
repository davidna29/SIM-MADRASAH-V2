<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    /**
     * Set a setting value (create or update).
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Get all settings as key => value collection.
     */
    public static function getAll(): Collection
    {
        return static::pluck('value', 'key');
    }

    public static function logoUrl(?Collection $settings = null): ?string
    {
        $path = $settings?->get('madrasah_logo') ?? static::get('madrasah_logo');

        if (! is_string($path) || $path === '') {
            return null;
        }

        $disk = Storage::disk('public');
        if ($disk->exists($path)) {
            return $disk->url($path);
        }

        $legacyPath = str_starts_with($path, 'settings/')
            ? 'settings/'.$path
            : 'settings/settings/'.$path;

        return $disk->exists($legacyPath) ? $disk->url($legacyPath) : null;
    }

    /**
     * Set multiple settings at once.
     */
    public static function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            static::set($key, $value);
        }
    }
}
