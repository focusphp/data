<?php

declare(strict_types=1);

namespace Focus\Data\Behavior;

use Focus\Data\Data;

/**
 * Provides simple source() method for a DataProxy extension
 *
 * @see \Focus\Data\DataProxy
 */
trait DataProxyBehavior
{
    public function __construct(
        private readonly Data $data,
    ) {
    }

    protected function source(): Data
    {
        return $this->data;
    }
}
