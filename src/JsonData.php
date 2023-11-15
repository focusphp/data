<?php

declare(strict_types=1);

namespace Focus\Data;

use Focus\Data\Behavior\DataProxyBehavior;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function json_decode;

use const JSON_THROW_ON_ERROR;

final readonly class JsonData extends DataProxy
{
    use DataProxyBehavior;

    public static function fromString(string $json): self
    {
        $data = KeyedData::tryFrom(json_decode($json, flags: JSON_THROW_ON_ERROR));

        return new self($data);
    }

    public static function fromRequest(RequestInterface $request, bool $useParsedBody = true): self
    {
        if ($request instanceof ServerRequestInterface && $useParsedBody) {
            $data = KeyedData::tryFrom($request->getParsedBody());

            return new self($data);
        }

        return self::fromString((string) $request->getBody());
    }

    public static function fromResponse(ResponseInterface $response): self
    {
        return self::fromString((string) $response->getBody());
    }
}
