<?php

declare(strict_types=1);

namespace Focus\Data\KeyedData;

use Focus\Data\Data;
use JmesPath\Env as JmesPath;
use RuntimeException;
use stdClass;

use function array_key_exists;
use function explode;
use function gettype;
use function is_array;
use function is_numeric;
use function is_object;
use function preg_match;
use function property_exists;
use function vsprintf;

final readonly class KeyedDataObject implements Data
{
    use CanExpandPath;

    public static function from(object $value): self
    {
        return new self($value);
    }

    public static function tryFrom(mixed $value): KeyedDataObject
    {
        if ($value === null) {
            return new self();
        }

        return KeyedDataFactory::from($value);
    }

    public function __construct(
        private object $value = new stdClass(),
    ) {
    }

    public function jsonSerialize(): object
    {
        return $this->value;
    }

    public function has(string $path): bool
    {
        $data = $this->value;

        foreach ($this->expand($path) as $key) {
            if (is_object($data)) {
                if (! property_exists($data, $key)) {
                    return false;
                }

                $data = $data->$key;
            } elseif (is_array($data) && is_numeric($key)) {
                if (! array_key_exists($key, $data)) {
                    return false;
                }

                $data = $data[$key];
            } else {
                return false;
            }
        }

        return true;
    }

    public function get(string $path): mixed
    {
        $data = $this->value;

        foreach ($this->expand($path) as $key) {
            if (is_object($data)) {
                $data = $data->$key ?? null;
            } elseif (is_array($data) && is_numeric($key)) {
                $data = $data[(int) $key] ?? null;
            } else {
                throw new RuntimeException(
                    message: vsprintf(format: 'Cannot follow path %s into type %s', values: [
                        $path,
                        gettype($data),
                    ]),
                );
            }
        }

        return $data;
    }

    public function search(string $path): mixed
    {
        if (preg_match(pattern: '/^[a-zA-Z0-9_.-]+$/', subject: $path)) {
            return $this->get($path);
        }

        return JmesPath::search($path, $this->value);
    }
}
