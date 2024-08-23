<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Focus\Data\JsonDataMutable;

$value = json_encode([
    'data' => [
        'invoice' => 10039,
        'amount' => '500.00',
        'due' => '2023-12-01',
    ],
]);

$data = JsonDataMutable::fromString($value);

var_dump($data->get(path: 'data.invoice'));
var_dump($data->get(path: 'data.amount'));
var_dump($data->get(path: 'data.due'));

$data->set(path: 'data.paid', value: false);
var_dump($data->get(path: 'data.paid'));

$data->set(path: 'data.due', value: '350.00');
var_dump($data->get(path: 'data.due'));
