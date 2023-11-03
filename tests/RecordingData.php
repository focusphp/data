<?php

declare(strict_types=1);

namespace Focus\Data\Tests;

use Focus\Data\Data;

final class RecordingData implements Data
{
    public array $calls = [];

    public function called(string $method): array
    {
        return $this->calls[$method] ?? [];
    }

    public function has(string $path): bool
    {
        $this->record(method: 'has', path: $path);

        return true;
    }

    public function get(string $path): null
    {
        $this->record(method: 'get', path: $path);

        return null;
    }

    public function search(string $path): null
    {
        $this->record(method: 'search', path: $path);

        return null;
    }

    public function jsonSerialize(): null
    {
        $this->record(method: 'jsonSerialize');

        return null;
    }

    private function record(string $method, string|null $path = null): void
    {
        $this->calls[$method][] = $path;
    }
}
