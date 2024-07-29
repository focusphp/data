# Focus: Data

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-8892BF.svg?style=flat)](https://php.net/)
[![Latest Stable Version](http://img.shields.io/packagist/v/focus/data.svg?style=flat)](https://packagist.org/packages/focus/data)
[![CI Status](https://github.com/focusphp/data/actions/workflows/ci.yml/badge.svg?branch=main&event=push)](https://github.com/focusphp/data/actions)
[![Code Coverage](https://codecov.io/gh/focusphp/data/graph/badge.svg?token=XFMRWA70FN)](https://codecov.io/gh/focusphp/data)

A collection of tools for working with unstructured data, such as JSON.

## Installation

The best way to install and use this package is with [composer](https://getcomposer.org/):

```shell
composer require focus/data
```

## Usage

The most basic usage is `KeyedDataObject`, which wraps objects and `KeyedDataArray`, which warps arrays:

```php
use Focus\Data\KeyedData\KeyedDataFactory;

$value = [
    'user' => [
        'name' => 'Susan Smith',
        'email' => 'susan@example.com',
        'hobbies' => [
            'football',
            'swimming',
            'reading',
        ],
        'deactivated_at' => null,
    ],
];

// Will create either KeyedDataObject or KeyedDataArray, depending on the value
$data = KeyedDataFactory::from($value);
```

Once you have an instance of data, you can access the values by using dot paths:

```php
$name = $data->get(path: 'user.name'); // Susan Smith
$email = $data->get(path: 'user.email'); // susan@example.com
```

Values that do not exist will be returned as `null`:

```php
$phone = $data->get(path: 'user.phone'); // null
```

[JMESPath](https://jmespath.org) expressions are also supported using the search() method:

```php
$sports = $data->search(path: "user.hobbies[? contains(@, 'ball')]"); // ['football']
```

It is also possible to check for the existence of a path, even when the value is `null`:

```php
$deactivated = $data->has(path: 'user.deactivated_at'); // true
```

### JSON Data

The `JsonData` object is a proxy with factory methods to create data instances
from JSON strings as well as PSR-7 `RequestInterface`, `ServerRequestInterface`,
and `ResponseInterface` objects:

```php
use Focus\Data\JsonData;

/** @var Psr\Http\Message\ServerRequestInterface $request */
$request = $app->request();

$data = JsonData::fromRequest($request);
```

There are three factory methods for `JsonData`:

- `fromString()` creates data from JSON strings
- `fromRequest()` creates data from PSR-7 (server) requests
- `fromResponse()` creates data from PSR-7 responses

Be aware when calling `JsonData::fromRequest()` with a `ServerRequestInterface` object,
the value of `getParsedBody()` will be used by default. To disable this behavior, use:

```php
$data = JsonData::fromRequest($request, useParsedBody: false);
```
When using `getParsedBody()` remember that most `ServerRequestInterface` objects will decode the request body 
with `associative: true`, producing an array. If you wish to have `JsonData->value` be an object, instead of an array,
configure your `ServerRequestInterface` to decode request bodies with `associative: false` (default for `json_decode()`)
## FAQ

These are some of the most common questions about usage and design of this package.

### Why does has() return true for null values?

This allows detecting when input has a value that should not be overwritten. For instance,
if an application sets a `deactivated_at` timestamp to indicate that the user has left,
it might also need to be able to reactivate the user by setting `deactivated_at: null`:

```php
if ($data->get(path: 'user.deactivated_at')) {
    $this->userRepository->deactivate(
        id: $data->get(path: 'user.id'),
        timestamp: $data->get(path: 'user.deactivated_at'),
    );
} elseif ($data->has(path: 'user.deactivated_at')) {
    $this->userRepository->activate(
        id: $data->get(path: 'user.id'),
    );
}
```

If has() did not return true for null values, detecting the existence of a null value would
be impossible, since get() returns null for undefined paths.

### Why is there a Data interface?

Keen observers will note that `KeyedData` implements a `Data` interface and the existence of
the `DataProxy` abstract class. This allows for customization of the implementation, despite
`KeyedData` being a `final readonly` class, by using a [proxy object][proxy] to satisfy the
[Open/Closed Principle][open-closed].

By default, the `DataProxy` object will forward all calls directly to the source `Data` object.
This allows customizing the behavior of any method without having to implement the full `Data`
interface. For example, this would modify the get() method to treat `false` values as `null`:

```php
use Focus\Data\Data;
use Focus\Data\DataProxy;

final class MyData extends DataProxy
{
    public function get(string $path): mixed
    {
        $value = $this->source()->get($path);
        
        if ($value === false) {
           return null;
        }
        
        return $value;
    }
}
```

[proxy]: https://refactoring.guru/design-patterns/proxy
[open-closed]: https://en.wikipedia.org/wiki/Open%E2%80%93closed_principle
