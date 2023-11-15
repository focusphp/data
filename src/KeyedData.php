<?php

declare(strict_types=1);

namespace Focus\Data;

use ArrayAccess;
use InvalidArgumentException;
use JmesPath\Env as JmesPath;
use RuntimeException;
use stdClass;

use function array_key_exists;
use function explode;
use function gettype;
use function is_array;
use function is_object;
use function preg_match;
use function property_exists;
use function trim;
use function vsprintf;

final readonly class KeyedData implements Data
{
    public static function from(array|object $value): self
    {
        return new self($value);
    }

    public static function tryFrom(mixed $value): self
    {
        if ($value === null) {
            return new self();
        }

        if (is_array($value) || is_object($value)) {
            return self::from($value);
        }

        throw new InvalidArgumentException(
            message: vsprintf(format: 'Cannot create KeyedData from %s', values: [
                gettype($value),
            ]),
        );
    }

    public function __construct(
        private array|object $value = new stdClass(),
    ) {
    }

    public function jsonSerialize(): array|object
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
            } elseif (is_object($data)) {
                if (! property_exists($data, $key)) {
                    return false;
                }

                $data = $data->$key;
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
            } elseif (is_object($data)) {
                $data = $data->$key ?? null;
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

    private function expand(string $path): array
    {
        // The path SHOULD be written as keys separated by periods.
        return explode(separator: '.', string: trim($path, characters: '.'));
    }
}
