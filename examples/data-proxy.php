<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Focus\Data\Behavior\DataProxyBehavior;
use Focus\Data\Data;
use Focus\Data\DataProxy;
use Focus\Data\KeyedData\KeyedDataFactory;

// phpcs:disable
final readonly class MyData extends DataProxy
{
    use DataProxyBehavior;

    public function has(string $path): bool
    {
        return parent::has($this->swapUsernameForEmail($path));
    }

    public function get(string $path): mixed
    {
        return parent::get($this->swapUsernameForEmail($path));
    }

    public function search(string $path): mixed
    {
        return parent::search($this->swapUsernameForEmail($path));
    }

    private function swapUsernameForEmail(string $path): string
    {
        return str_replace(search: '.username', replace: '.email', subject: $path);
    }
}
// phpcs:enable


$value = [
    'data' => [
        'username' => 'billy-ignored',
        'email' => 'billy@example.com',
    ],
];

// While this looks like normal OOP abstraction, the key difference here is that
// the underlying Data object can be any implementation.
$data = new MyData(KeyedDataFactory::from($value));

assert(assertion: $data instanceof Data);

// get(), has(), and search() should return the "email" value instead of "username".
var_dump($data->has(path: 'data.username'));
var_dump($data->get(path: 'data.username'));
var_dump($data->search(path: 'data.username'));
