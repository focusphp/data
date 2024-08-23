<?php

declare(strict_types=1);

namespace Focus\Data\KeyedData;

use function explode;
use function trim;

trait CanExpandPath
{
    private function expand(string $path): array
    {
        // The path SHOULD be written as keys separated by periods.
        return explode(separator: '.', string: trim($path, characters: '.'));
    }
}
