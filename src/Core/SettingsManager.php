<?php

namespace Monarul007\LaravelModularSystem\Core;

use Illuminate\Support\Facades\Cache;
use Monarul007\LaravelModularSystem\Models\Setting;

class SettingsManager
{
    protected array $settings = [];

    public function get(string $key, mixed $default = null): mixed
    {
        if (isset($this->settings[$key])) {
            return $this->settings[$key];
        }

        $cacheKey = "modular_system.setting.{$key}";
        $cacheTtl = config('modular-system.cache_ttl', 3600);

        $value = config('modular-system.cache_enabled', true)
            ? Cache::remember($cacheKey, $cacheTtl, fn() => $this->fetchSetting($key, $default))
            : $this->fetchSetting($key, $default);

        $this->settings[$key] = $value;
        return $value;
    }

    protected function fetchSetting(string $key, mixed $default): mixed
    {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $this->castValue($setting->value, $setting->type) : $default;
    }

    public function set(string $key, mixed $value, string $group = 'general'): void
    {
        $type = $this->getValueType($value);
        
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $this->prepareValue($value, $type),
                'type' => $type,
                'group' => $group
            ]
        );

        $this->settings[$key] = $value;
        
        if (config('modular-system.cache_enabled', true)) {
            Cache::forget("modular_system.setting.{$key}");
        }
    }

    public function getGroup(string $group): array
    {
        $cacheKey = "modular_system.settings.group.{$group}";
        $cacheTtl = config('modular-system.cache_ttl', 3600);

        return config('modular-system.cache_enabled', true)
            ? Cache::remember($cacheKey, $cacheTtl, fn() => $this->fetchGroup($group))
            : $this->fetchGroup($group);
    }

    protected function fetchGroup(string $group): array
    {
        $settings = Setting::where('group', $group)->get();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->key] = $this->castValue($setting->value, $setting->type);
        }
        
        return $result;
    }

    public function forget(string $key): void
    {
        Setting::where('key', $key)->delete();
        unset($this->settings[$key]);
        
        if (config('modular-system.cache_enabled', true)) {
            Cache::forget("modular_system.setting.{$key}");
        }
    }

    protected function getValueType(mixed $value): string
    {
        return match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_float($value) => 'float',
            is_array($value) => 'array',
            default => 'string'
        };
    }

    protected function prepareValue(mixed $value, string $type): string
    {
        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'array' => json_encode($value),
            default => (string) $value
        };
    }

    protected function castValue(string $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            'array' => json_decode($value, true),
            default => $value
        };
    }
}
