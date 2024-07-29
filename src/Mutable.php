<?php

declare(strict_types=1);

namespace Focus\Data;

interface Mutable extends Data
{
    /**
     * Set the data contained in the given path
     *
     * The $path MUST consist of appropriate keys for the underlying data,
     * separated by single periods. This is referred to as a "dot path" and is
     * similar to Javascript (and Python, etc.) object access notation.
     *
     * The $value can be any valid type that will replace the value stored in
     * the given path.
     */
    public function set(string $path, mixed $value): void;
}
