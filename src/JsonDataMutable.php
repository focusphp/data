<?php

declare(strict_types=1);

namespace Focus\Data;

use Focus\Data\Behavior\DataProxyMutableBehavior;
use Focus\Data\KeyedData\CanExpandPath;
use Focus\Data\KeyedData\KeyedDataArray;
use Focus\Data\KeyedData\KeyedDataFactory;
use Focus\Data\KeyedData\KeyedDataObject;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

use function array_pop;
use function json_decode;

use const JSON_THROW_ON_ERROR;

class JsonDataMutable extends DataProxyMutable
{
    use DataProxyMutableBehavior;
    use CanExpandPath;

    public static function fromString(string $json, bool $associative = false): self
    {
        $data = json_decode(json: $json, associative: $associative, flags: JSON_THROW_ON_ERROR);

        $data = KeyedDataFactory::from($data);

        return new self($data);
    }

    public static function fromRequest(RequestInterface $request, bool $useParsedBody = true, bool $associative = true): self
    {
        if ($request instanceof ServerRequestInterface && $useParsedBody) {
            $data = KeyedDataFactory::from($request->getParsedBody());

            return new self($data);
        }

        return self::fromString((string) $request->getBody(), $associative);
    }

    public static function fromResponse(ResponseInterface $response, bool $associative = false): self
    {
        return self::fromString((string) $response->getBody(), $associative);
    }

    public function set(string $path, mixed $value): void
    {
        $keyPath = $this->expand($path);

        $endKey = array_pop($keyPath);

        if ($this->source() instanceof KeyedDataArray) {
            $data = $this->setKeyedDataArray(keyPath: $keyPath, data: $this->data, value: $value, endKey: $endKey);
        }

        if ($this->source() instanceof KeyedDataObject) {
            $data = $this->setKeyedDataObject(keyPath: $keyPath, data: $this->data, value: $value, endKey: $endKey);
        }

        $this->data = KeyedDataFactory::from($data);
    }

    private function setKeyedDataObject(array $keyPath, KeyedDataObject $data, mixed $value, string $endKey): object
    {
        $data = $data->jsonSerialize();
        $currentValue =& $data;

        foreach ($keyPath as $currentKey) {
            if (! isset($currentValue->$currentKey)) {
                $currentValue->$currentKey = new stdClass();
            }

            $currentValue =& $currentValue->$currentKey;
        }

        $currentValue->$endKey = $value;

        return $data;
    }

    private function setKeyedDataArray(array $keyPath, KeyedDataArray $data, mixed $value, string $endKey): array
    {
        $data = $data->jsonSerialize();
        $currentValue =& $data;

        foreach ($keyPath as $currentKey) {
            if (! isset($currentValue[$currentKey])) {
                $currentValue[$currentKey] = [];
            }

            $currentValue =& $currentValue[$currentKey];
        }

        $currentValue[$endKey] = $value;

        return $data;
    }
}
