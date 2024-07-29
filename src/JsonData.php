<?php

declare(strict_types=1);

namespace Focus\Data;

use Focus\Data\Behavior\DataProxyBehavior;
use Focus\Data\KeyedData\KeyedDataFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function json_decode;

use const JSON_THROW_ON_ERROR;

final readonly class JsonData extends DataProxy
{
    use DataProxyBehavior;

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
}
