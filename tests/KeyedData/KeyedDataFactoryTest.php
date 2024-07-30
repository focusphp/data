<?php

declare(strict_types=1);

namespace Focus\Data\Tests\KeyedData;

use ArrayObject;
use Focus\Data\KeyedData\KeyedDataArray;
use Focus\Data\KeyedData\KeyedDataFactory;
use Focus\Data\KeyedData\KeyedDataObject;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function json_decode;
use function json_encode;

#[CoversClass(KeyedDataFactory::class)]
#[CoversClass(KeyedDataObject::class)]
#[CoversClass(KeyedDataArray::class)]
class KeyedDataFactoryTest extends TestCase
{
    private static array $fixture = [
        'name' => 'test',
        'age' => 66,
        'children' => [
            'name' => 'mike',
            'age' => '12',
        ],
    ];

    public function testCanCreateKeyedDataObject(): void
    {
        $body = json_decode(json_encode(self::$fixture));

        $data = KeyedDataFactory::from($body);

        self::assertInstanceOf(KeyedDataObject::class, $data);
    }

    public function testCanCreateKeyedDataArray(): void
    {
        $data = KeyedDataFactory::from(new ArrayObject(self::$fixture));

        self::assertInstanceOf(KeyedDataArray::class, $data);

        $data = KeyedDataFactory::from(self::$fixture);

        self::assertInstanceOf(KeyedDataArray::class, $data);
    }
}
