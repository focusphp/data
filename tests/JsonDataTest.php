<?php

declare(strict_types=1);

namespace Focus\Data\Tests;

use Focus\Data\DataProxy;
use Focus\Data\JsonData;
use Focus\Data\KeyedData\KeyedDataArray;
use Focus\Data\KeyedData\KeyedDataFactory;
use Focus\Data\KeyedData\KeyedDataObject;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

use function json_decode;
use function json_encode;

#[CoversClass(JsonData::class)]
#[UsesClass(DataProxy::class)]
#[UsesClass(KeyedDataObject::class)]
#[UsesClass(KeyedDataArray::class)]
#[UsesClass(KeyedDataFactory::class)]
class JsonDataTest extends TestCase
{
    private Psr17Factory $httpFactory;

    protected function setUp(): void
    {
        $this->httpFactory = new Psr17Factory();
    }

    public function testShouldCreateFromString(): void
    {
        $data = JsonData::fromString(json: '{"test": true}');

        self::assertTrue(
            condition: $data->get(path: 'test'),
        );
    }

    public function testShouldCreateFromRequest(): void
    {
        $request = $this->httpFactory->createRequest(method: 'GET', uri: '/');
        $request = $request->withBody($this->httpFactory->createStream(content: '{"test": true}'));

        $data = JsonData::fromRequest($request);

        self::assertTrue(
            condition: $data->get(path: 'test'),
        );
    }

    public function testShouldCreateFromServerRequest(): void
    {
        $request = $this->httpFactory->createServerRequest(method: 'GET', uri: '/');
        $request = $request->withParsedBody(data: ['test' => true]);

        $data = JsonData::fromRequest($request);

        self::assertTrue(
            condition: $data->get(path: 'test'),
        );
    }

    public function testShouldCreateFromServerRequestWhenParsedBodyIsObject(): void
    {
        $request = $this->httpFactory->createServerRequest(method: 'GET', uri: '/');
        $request = $request->withParsedBody(data: json_decode(json_encode(['test' => true])));

        $data = JsonData::fromRequest($request, associative: false);

        self::assertTrue(
            condition: $data->get(path: 'test'),
        );
    }

    public function testShouldCreateFromServerRequestAndForceParsing(): void
    {
        $request = $this->httpFactory->createServerRequest(method: 'GET', uri: '/');
        $request = $request->withBody($this->httpFactory->createStream(content: '{"test": true}'));
        $request = $request->withParsedBody(data: ['test' => false]);

        $data = JsonData::fromRequest($request, useParsedBody: false);

        self::assertTrue(
            condition: $data->get(path: 'test'),
        );
    }

    public function testShouldCreateFromResponse(): void
    {
        $response = $this->httpFactory->createResponse(code: 200);
        $response = $response->withBody($this->httpFactory->createStream(content: '{"test": true}'));

        $data = JsonData::fromResponse($response);

        self::assertTrue(
            condition: $data->get(path: 'test'),
        );
    }
}
