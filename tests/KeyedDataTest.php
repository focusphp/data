<?php

declare(strict_types=1);

namespace Focus\Data\Tests;

use ArrayObject;
use Focus\Data\KeyedData;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function array_map;
use function json_decode;
use function json_encode;
use function vsprintf;

#[CoversClass(KeyedData::class)]
class KeyedDataTest extends TestCase
{
    private static array $fixture = [
        'testing' => [
            'none' => null,
            'requires' => [
                'nesting' => false,
                'patience' => 'always',
            ],
            'list' => [1, 2, 3],
        ],
        'codes' => [
            '01A' => 100,
            'B3A' => 200,
        ],
        'top' => true,
    ];

    public static function supportedValues(): array
    {
        $set = array_map(KeyedData::from(...), [
            'array' => self::$fixture,
            'ArrayAccess' => new ArrayObject(self::$fixture),
            'stdClass' => json_decode(json_encode(self::$fixture)),
        ]);

        return array_map(static fn (KeyedData $x) => [$x], $set);
    }

    public static function unsupportedValues(): array
    {
        return [
            'string' => ['test', 'string'],
            'integer' => [100000, 'integer'],
            'float' => [3.14, 'double'],
        ];
    }

    public function testFromShouldCreateInstance(): void
    {
        $data = KeyedData::from(self::$fixture);

        self::assertInstanceOf(
            expected: KeyedData::class,
            actual: $data,
        );
    }

    public function testTryFromShouldCreateInstance(): void
    {
        $data = KeyedData::tryFrom(self::$fixture);

        self::assertInstanceOf(
            expected: KeyedData::class,
            actual: $data,
        );
    }

    public function testTryFromShouldCreateEmptyObjectForNull(): void
    {
        $data = KeyedData::tryFrom(value: null);

        self::assertInstanceOf(
            expected: KeyedData::class,
            actual: $data,
        );

        self::assertSame(
            expected: '{}',
            actual: json_encode($data),
        );
    }

    #[DataProvider(methodName: 'unsupportedValues')]
    public function testTryFromShouldThrowInvalidArgumentExceptionOnInvalidType(mixed $value, string $type): void
    {
        self::expectException(
            exception: InvalidArgumentException::class,
        );

        self::expectExceptionMessage(
            message: vsprintf(format: 'Cannot create KeyedData from %s', values: [
                $type,
            ]),
        );

        KeyedData::tryFrom($value);
    }

    #[DataProvider(methodName: 'supportedValues')]
    public function testJsonSerializeShouldBeUsedForEncoding(KeyedData $data): void
    {
        self::assertJson(
            actual: json_encode($data),
        );
    }

    #[DataProvider(methodName: 'supportedValues')]
    public function testHasShouldDetermineKeyExistence(KeyedData $data): void
    {
        self::assertFalse(
            condition: $data->has(path: 'noop'),
            message: 'has() should return false for paths that are undefined',
        );

        self::assertTrue(
            condition: $data->has(path: 'top'),
            message: 'has() should return true for paths that are set',
        );

        self::assertTrue(
            condition: $data->has(path: 'testing.none'),
            message: 'has() should return true for nested paths that are null',
        );

        self::assertTrue(
            condition: $data->has(path: 'testing.requires.nesting'),
            message: 'has() should return true for nested paths that are set',
        );

        self::assertFalse(
            condition: $data->has(path: 'testing.requires.nesting.deeply'),
            message: 'has() should return false when encountering a path that is not traversable',
        );
    }

    #[DataProvider(methodName: 'supportedValues')]
    public function testGetShouldReturnValueAtPath(KeyedData $data): void
    {
        self::assertNull(
            actual: $data->get(path: 'noop'),
            message: 'get() should return null for paths that are undefined',
        );

        self::assertTrue(
            condition: $data->get(path: 'top'),
            message: 'get() should return the value of a path',
        );

        self::assertSame(
            expected: 100,
            actual: $data->search(path: 'codes.01A'),
            message: 'get() should return the value of a key that starts with a number',
        );

        self::assertFalse(
            condition: $data->get(path: 'testing.requires.nesting'),
            message: 'get() should return the boolean of a path',
        );

        self::assertSame(
            expected: 'always',
            actual: $data->get(path: 'testing.requires.patience'),
            message: 'get() should return the value of a path',
        );

        self::assertSame(
            expected: 2,
            actual: $data->get(path: 'testing.list.1'),
            message: 'get() should return the value of a path with numeric keys',
        );
    }

    #[DataProvider(methodName: 'supportedValues')]
    public function testGetShouldThrowRuntimeExceptionPathsThatAreNotTraversable(KeyedData $data): void
    {
        self::expectException(
            exception: RuntimeException::class,
        );

        self::expectExceptionMessage(
            message: 'Cannot follow path top.fail into type boolean',
        );

        $data->get(path: 'top.fail');
    }

    #[DataProvider(methodName: 'supportedValues')]
    public function testSearchShouldReturnValueByDotPathOrExpression(KeyedData $data): void
    {
        self::assertNull(
            actual: $data->search(path: 'noop'),
            message: 'search() should return null for paths that are undefined',
        );

        self::assertTrue(
            condition: $data->search(path: 'top'),
            message: 'search() should return the value of a path',
        );

        self::assertSame(
            expected: 100,
            actual: $data->search(path: 'codes.01A'),
            message: 'search() should return the value of a key that starts with a number',
        );

        self::assertSame(
            expected: 3,
            actual: $data->search(path: 'max(testing.list)'),
            message: 'search() should return the result of JMESPath query',
        );
    }
}
