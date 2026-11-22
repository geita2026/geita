<?php

namespace App\Support;

use Illuminate\Support\Str;

class PermissionNames
{
    public static function aliases(): array
    {
        $aliases = [];
        foreach (config('navigation') as [, , , $submenus]) {
            foreach ($submenus as [, , $actions]) {
                $aliases += $actions;
            }
        }

        $lookup = $aliases;
        $normalized = [];
        foreach ($aliases as $label => $key) {
            $normalized[self::normalize($label)][$key] = true;
        }
        foreach (array_unique(array_values($aliases)) as $key) {
            $lookup[$key] = $key;
        }
        foreach ($normalized as $name => $targets) {
            if (count($targets) === 1) {
                $lookup[$name] ??= array_key_first($targets);
            }
        }

        return $lookup;
    }

    public static function canonical(string $name, ?array $aliases = null): string
    {
        $lookup = $aliases ?? self::aliases();
        $normalized = self::normalize($name);

        return $lookup[$name] ?? $lookup[$normalized] ?? $normalized;
    }

    private static function normalize(string $name): string
    {
        $name = Str::ascii($name);
        $name = preg_replace('/(?<=[a-z0-9])(?=[A-Z])/', '_', $name);

        return strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $name), '_'));
    }
}
