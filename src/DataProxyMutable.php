<?php

declare(strict_types=1);

namespace Focus\Data;

abstract class DataProxyMutable implements Mutable
{
    abstract protected function source(): Data;

    public function has(string $path): bool
    {
        return $this->source()->has($path);
    }

    public function get(string $path): mixed
    {
        return $this->source()->get($path);
    }

    public function search(string $path): mixed
    {
        return $this->source()->search($path);
    }

    public function jsonSerialize(): mixed
    {
        return $this->source()->jsonSerialize();
    }
}
