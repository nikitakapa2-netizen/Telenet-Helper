<?php

declare(strict_types=1);

function loadEnv(string $path): array
{
    $config = [];

    if (!file_exists($path)) {
        return $config;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $config[trim($key)] = trim($value);
    }

    return $config;
}

function env(string $key, mixed $default = null): mixed
{
    static $config = null;

    if ($config === null) {
        $envPath = dirname(__DIR__) . '/.env';
        $config = loadEnv($envPath);
    }

    return $config[$key] ?? $default;
}
