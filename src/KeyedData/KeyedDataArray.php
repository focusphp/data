<?php

declare(strict_types=1);

namespace Focus\Data\KeyedData;

use ArrayAccess;
use Focus\Data\Data;
use JmesPath\Env as JmesPath;
use RuntimeException;

use function array_key_exists;
use function gettype;
use function is_array;
use function preg_match;
use function vsprintf;

final readonly class KeyedDataArray implements Data
{
    use CanExpandPath;

    public static function from(array|ArrayAccess $value): self
    {
        return new self($value);
    }

    public static function tryFrom(mixed $value): KeyedDataArray
    {
        if ($value === null) {
            return new self();
        }

        return KeyedDataFactory::from($value);
    }

    public function __construct(
        private array|ArrayAccess $value = [],
    ) {
    }

    public function jsonSerialize(): array|ArrayAccess
    {
        return $this->value;
    }

    public function has(string $path): bool
    {
        $data = $this->value;

        foreach ($this->expand($path) as $key) {
            if ($data instanceof ArrayAccess) {
                if (! $data->offsetExists($key)) {
                    return false;
                }

                $data = $data->offsetGet($key);
            } elseif (is_array($data)) {
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
            if (is_array($data) || $data instanceof ArrayAccess) {
                $data = $data[$key] ?? null;
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
