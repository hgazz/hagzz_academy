<?php

namespace App\Support;

class StorageUrl
{
    public static function asset(?string $value, string $path, ?string $fallback = null): string
    {
        if (blank($value)) {
            return $fallback ?? '';
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return rtrim(config('services.storage.url'), '/') . '/' . trim($path, '/') . '/' . ltrim($value, '/');
    }
}
