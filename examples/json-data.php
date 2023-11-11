<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Focus\Data\JsonData;

$value = json_encode([
    'data' => [
        'invoice' => 10039,
        'amount' => '500.00',
        'due' => '2023-12-01',
    ],
]);

$data = JsonData::fromString($value);

var_dump($data->get(path: 'data.invoice'));
var_dump($data->get(path: 'data.amount'));
var_dump($data->get(path: 'data.due'));
