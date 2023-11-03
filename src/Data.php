<?php

declare(strict_types=1);

namespace Focus\Data;

use JsonSerializable;

interface Data extends JsonSerializable
{
    /**
     * Check if the given path exists
     *
     * This method MUST return true when the $path points to a null value,
     * similar to array_key_exists() or property_exists().
     *
     * @link https://php.net/array_key_exists
     * @link https://php.net/property_exists
     **/
    public function has(string $path): bool;

    /**
     * Return the value at the given path
     *
     * The $path MUST consist of appropriate keys for the underlying data,
     * separated by single periods. This is referred to as a "dot path" and is
     * similar to Javascript (and Python, etc.) object access notation.
     */
    public function get(string $path): mixed;

    /**
     * Returns the value found at the given path or expression
     *
     * The $path MAY be either a dot path or a JMESPath expression.
     *
     * When the $path consists of only letters, numbers, underscores, and periods
     * it SHOULD be handled as a get() call. Otherwise, the $path SHOULD be handled
     * as a JMESPath query.
     *
     * While a JMESPath expressions allows for dot paths, it requires that keys
     * starting with numbers be quoted, as in `codes."0ABC".value`. This can be
     * particularly awkward when composing paths dynamically.
     *
     * @link https://jmespath.org/
     */
    public function search(string $path): mixed;
}
