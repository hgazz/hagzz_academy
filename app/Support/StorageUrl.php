<?php

namespace App\Support;

class StorageUrl
{
    public static function asset(?string $value, string $path, ?string $fallback = null): string
    {
        if (blank($value)) {
            return $fallback ?? '';
        }

        $trimmed = trim($value);

        if (str_contains($trimmed, 'data:image')) {
            $pos = strpos($trimmed, 'data:image');
            return substr($trimmed, $pos);
        }

        if (str_starts_with($trimmed, 'data:') || str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://') || filter_var($trimmed, FILTER_VALIDATE_URL)) {
            return $trimmed;
        }

        return rtrim(config('services.storage.url'), '/') . '/' . trim($path, '/') . '/' . ltrim($trimmed, '/');
    }
}
