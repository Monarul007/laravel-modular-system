<?php

namespace Monarul007\LaravelModularSystem\Core;

use Illuminate\Support\Facades\Cache;

class FeatureRegistry
{
    protected array $features = [];

    public function register(string $featureKey, array $config): void
    {
        $this->features[$featureKey] = array_merge([
            'enabled' => true,
            'module' => null,
            'permissions' => [],
            'dependencies' => [],
        ], $config);

        $this->clearCache();
    }

    public function isEnabled(string $featureKey): bool
    {
        if (!isset($this->features[$featureKey])) {
            return false;
        }

        $feature = $this->features[$featureKey];

        if (!$feature['enabled']) {
            return false;
        }

        if (!empty($feature['dependencies'])) {
            foreach ($feature['dependencies'] as $dependency) {
                if (!$this->isEnabled($dependency)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function enable(string $featureKey): bool
    {
        if (!isset($this->features[$featureKey])) {
            return false;
        }

        $this->features[$featureKey]['enabled'] = true;
        $this->clearCache();

        return true;
    }

    public function disable(string $featureKey): bool
    {
        if (!isset($this->features[$featureKey])) {
            return false;
        }

        $this->features[$featureKey]['enabled'] = false;
        $this->clearCache();

        return true;
    }

    public function get(string $featureKey): ?array
    {
        return $this->features[$featureKey] ?? null;
    }

    public function all(): array
    {
        return $this->features;
    }

    public function getByModule(string $moduleName): array
    {
        return array_filter($this->features, function ($feature) use ($moduleName) {
            return ($feature['module'] ?? null) === $moduleName;
        });
    }

    public function unregister(string $featureKey): void
    {
        unset($this->features[$featureKey]);
        $this->clearCache();
    }

    protected function clearCache(): void
    {
        if (config('modular-system.cache_enabled', true)) {
            Cache::forget('modular_system.features');
        }
    }
}
