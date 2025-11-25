<?php

namespace Monarul007\LaravelModularSystem\Core;

class DependencyResolver
{
    public function __construct(protected ModuleManager $moduleManager)
    {
    }

    public function checkDependencies(string $moduleName): array
    {
        $config = $this->moduleManager->getModuleConfig($moduleName);
        $missing = [];

        if (!$config || empty($config['dependencies'])) {
            return [];
        }

        foreach ($config['dependencies'] as $dependency => $constraint) {
            if (is_numeric($dependency)) {
                $dependency = $constraint;
                $constraint = '*';
            }

            if (!$this->moduleManager->isModuleEnabled($dependency)) {
                $missing[] = $dependency;
                continue;
            }

            if ($constraint !== '*' && !$this->satisfiesVersion($dependency, $constraint)) {
                $missing[] = "$dependency ($constraint)";
            }
        }

        return $missing;
    }

    public function detectCircularDependencies(string $moduleName, array $visited = []): ?array
    {
        if (in_array($moduleName, $visited)) {
            return array_merge($visited, [$moduleName]);
        }

        $config = $this->moduleManager->getModuleConfig($moduleName);
        if (!$config || empty($config['dependencies'])) {
            return null;
        }

        $visited[] = $moduleName;

        foreach ($config['dependencies'] as $dependency => $constraint) {
            if (is_numeric($dependency)) {
                $dependency = $constraint;
            }

            $circular = $this->detectCircularDependencies($dependency, $visited);
            if ($circular) {
                return $circular;
            }
        }

        return null;
    }

    public function getDependentModules(string $moduleName): array
    {
        $dependents = [];
        $enabledModules = $this->moduleManager->getEnabledModules();

        foreach ($enabledModules as $module) {
            if ($module === $moduleName) {
                continue;
            }

            $config = $this->moduleManager->getModuleConfig($module);
            if (!$config || empty($config['dependencies'])) {
                continue;
            }

            foreach ($config['dependencies'] as $dependency => $constraint) {
                if (is_numeric($dependency)) {
                    $dependency = $constraint;
                }

                if ($dependency === $moduleName) {
                    $dependents[] = $module;
                    break;
                }
            }
        }

        return $dependents;
    }

    protected function satisfiesVersion(string $moduleName, string $constraint): bool
    {
        $config = $this->moduleManager->getModuleConfig($moduleName);
        if (!$config || !isset($config['version'])) {
            return false;
        }

        $version = $config['version'];

        if ($constraint === '*') {
            return true;
        }

        if (strpos($constraint, '^') === 0) {
            return $this->satisfiesCaretConstraint($version, substr($constraint, 1));
        }

        if (strpos($constraint, '~') === 0) {
            return $this->satisfiesTildeConstraint($version, substr($constraint, 1));
        }

        if (strpos($constraint, '>=') === 0) {
            return version_compare($version, substr($constraint, 2), '>=');
        }

        if (strpos($constraint, '>') === 0) {
            return version_compare($version, substr($constraint, 1), '>');
        }

        if (strpos($constraint, '<=') === 0) {
            return version_compare($version, substr($constraint, 2), '<=');
        }

        if (strpos($constraint, '<') === 0) {
            return version_compare($version, substr($constraint, 1), '<');
        }

        return version_compare($version, $constraint, '=');
    }

    protected function satisfiesCaretConstraint(string $version, string $constraint): bool
    {
        $versionParts = explode('.', $version);
        $constraintParts = explode('.', $constraint);

        if ($versionParts[0] !== $constraintParts[0]) {
            return false;
        }

        return version_compare($version, $constraint, '>=');
    }

    protected function satisfiesTildeConstraint(string $version, string $constraint): bool
    {
        $versionParts = explode('.', $version);
        $constraintParts = explode('.', $constraint);

        if ($versionParts[0] !== $constraintParts[0] || $versionParts[1] !== $constraintParts[1]) {
            return false;
        }

        return version_compare($version, $constraint, '>=');
    }
}
