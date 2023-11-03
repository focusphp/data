<?php

declare(strict_types=1);

namespace Focus\Data\Tests;

use Focus\Data\Data;
use Focus\Data\DataProxy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DataProxy::class)]
class DataProxyTest extends TestCase
{
    private RecordingData $data;
    private DataProxy $proxy;

    protected function setUp(): void
    {
        $this->data = new RecordingData();
        $this->proxy = new class ($this->data) extends DataProxy {
            public function __construct(
                private readonly Data $data,
            ) {
            }

            protected function source(): Data
            {
                return $this->data;
            }
        };
    }

    public function testHasShouldProxy(): void
    {
        $this->proxy->has(path: 'test');

        self::assertCount(
            expectedCount: 1,
            haystack: $this->data->called(method: 'has'),
        );
    }

    public function testGetShouldProxy(): void
    {
        $this->proxy->get(path: 'test');

        self::assertCount(
            expectedCount: 1,
            haystack: $this->data->called(method: 'get'),
        );
    }

    public function testSearchShouldProxy(): void
    {
        $this->proxy->search(path: 'test');

        self::assertCount(
            expectedCount: 1,
            haystack: $this->data->called(method: 'search'),
        );
    }

    public function testJsonSerializeShouldProxy(): void
    {
        $this->proxy->jsonSerialize();

        self::assertCount(
            expectedCount: 1,
            haystack: $this->data->called(method: 'jsonSerialize'),
        );
    }
}
