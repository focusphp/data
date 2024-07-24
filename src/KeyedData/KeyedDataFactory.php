<?php

declare(strict_types=1);

namespace Focus\Data\KeyedData;

use ArrayAccess;
use InvalidArgumentException;

use function gettype;
use function is_array;
use function is_object;
use function vsprintf;

final readonly class KeyedDataFactory
{
    public static function from(mixed $value): KeyedDataArray|KeyedDataObject
    {
        if (is_array($value) || $value instanceof ArrayAccess) {
            return new KeyedDataArray($value);
        }

        if (is_object($value)) {
            return new KeyedDataObject($value);
        }

        throw new InvalidArgumentException(
            message: vsprintf(format: 'Cannot create KeyedDataObject or KeyedDataArray from %s', values: [
                gettype($value),
            ]),
        );
    }
}
