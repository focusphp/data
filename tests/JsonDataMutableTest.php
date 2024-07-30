<?php

declare(strict_types=1);

namespace Focus\Data\Tests;

use Focus\Data\DataProxyMutable;
use Focus\Data\JsonDataMutable;
use Focus\Data\KeyedData\KeyedDataArray;
use Focus\Data\KeyedData\KeyedDataFactory;
use Focus\Data\KeyedData\KeyedDataObject;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

use function json_decode;
use function json_encode;

#[CoversClass(JsonDataMutable::class)]
#[CoversClass(DataProxyMutable::class)]
#[UsesClass(KeyedDataObject::class)]
#[UsesClass(KeyedDataArray::class)]
#[UsesClass(KeyedDataFactory::class)]
class JsonDataMutableTest extends TestCase
{
    private Psr17Factory $httpFactory;

    protected function setUp(): void
    {
        $this->httpFactory = new Psr17Factory();
    }

    public function testShouldCreateFromString(): void
    {
        $data = JsonDataMutable::fromString(json: '{"test": true}');

        self::assertTrue(
            condition: $data->get(path: 'test'),
        );
    }

    public function testShouldCreateFromRequest(): void
    {
        $request = $this->httpFactory->createRequest(method: 'GET', uri: '/');
        $request = $request->withBody($this->httpFactory->createStream(content: '{"test": true}'));

        $data = JsonDataMutable::fromRequest($request);

        self::assertTrue(
            condition: $data->get(path: 'test'),
        );
    }

    public function testShouldCreateFromServerRequest(): void
    {
        $request = $this->httpFactory->createServerRequest(method: 'GET', uri: '/');
        $request = $request->withParsedBody(data: ['test' => true]);

        $data = JsonDataMutable::fromRequest($request);

        self::assertTrue(
            condition: $data->get(path: 'test'),
        );
    }

    public function testShouldCreateFromServerRequestWhenParsedBodyIsObject(): void
    {
        $request = $this->httpFactory->createServerRequest(method: 'GET', uri: '/');
        $request = $request->withParsedBody(data: json_decode(json_encode(['test' => true])));

        $data = JsonDataMutable::fromRequest($request, associative: false);

        self::assertTrue(
            condition: $data->get(path: 'test'),
        );
    }

    public function testShouldCreateFromServerRequestAndForceParsing(): void
    {
        $request = $this->httpFactory->createServerRequest(method: 'GET', uri: '/');
        $request = $request->withBody($this->httpFactory->createStream(content: '{"test": true}'));
        $request = $request->withParsedBody(data: ['test' => false]);

        $data = JsonDataMutable::fromRequest($request, useParsedBody: false);

        self::assertTrue(
            condition: $data->get(path: 'test'),
        );
    }

    public function testShouldCreateFromResponse(): void
    {
        $response = $this->httpFactory->createResponse(code: 200);
        $response = $response->withBody($this->httpFactory->createStream(content: '{"test": true}'));

        $data = JsonDataMutable::fromResponse($response);

        self::assertTrue(
            condition: $data->get(path: 'test'),
        );
    }

    public function testShouldSetDataIfPathNotExistObject(): void
    {
        $body = [
            'name' => 'root',
            'age' => 32,
            'children' => [
                [
                    'name' => 'child1',
                    'age' => 12,
                ],
            ],
        ];

        $request = $this->httpFactory->createServerRequest(method: 'GET', uri: '/');
        $request = $request->withParsedBody(data: json_decode(json_encode($body)));

        $data = JsonDataMutable::fromRequest($request);

        $data->set('attributes.speed', 3);

        self::assertIsObject($data->jsonSerialize());
        self::assertSame(3, $data->get('attributes.speed'));
    }

    public function testShouldSetDataIfPathNotExistArray(): void
    {
        $body = [
            'name' => 'root',
            'age' => 32,
            'children' => [
                [
                    'name' => 'child1',
                    'age' => 12,
                ],
            ],
        ];

        $request = $this->httpFactory->createServerRequest(method: 'GET', uri: '/');
        $request = $request->withParsedBody(data: $body);

        $data = JsonDataMutable::fromRequest($request);

        $data->set('attributes.speed', 3);

        self::assertTrue($data->has('attributes.speed'));
        self::assertSame(3, $data->get('attributes.speed'));
    }

    public function testShouldSetData(): void
    {
        $body = [
            'name' => 'root',
            'age' => 32,
            'child' => [
                'name' => 'child1',
                'age' => 12,
            ],
        ];

        $request = $this->httpFactory->createServerRequest(method: 'GET', uri: '/');
        $request = $request->withParsedBody(data: $body);

        $data = JsonDataMutable::fromRequest($request);

        $data->set('age', 50);
        $data->set('child.age', 25);

        self::assertSame(50, $data->get('age'));
        self::assertSame(25, $data->search('child.age'));
    }
}
