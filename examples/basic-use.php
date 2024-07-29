<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

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

// If the value is an object, then a KeyedDataObject will be returned.
// If the value is an array or ArrayObject, then a KeyedDataArray will be returned.
$data = KeyedDataFactory::from($value);

// get() returns values using dot paths
$name = $data->get(path: 'user.name');
$email = $data->get(path: 'user.email');
$phone = $data->get(path: 'user.phone');

var_dump($name);
var_dump($email);
var_dump($phone);

// search() allows for JMESPath expression queries
$sports = $data->search(path: "user.hobbies[? contains(@, 'ball')]");

var_dump($sports);

// has() returns true for null values
var_dump($data->has(path: 'user.deactivated_at'));
var_dump($data->get(path: 'user.deactivated_at'));
