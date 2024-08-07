<?php

declare(strict_types=1);

namespace Focus\Data\Behavior;

use Focus\Data\Data;
use Focus\Data\KeyedData\KeyedDataObject;

/**
 * Provides simple source() method for a DataProxy extension
 *
 * @see \Focus\Data\DataProxy
 */
trait DataProxyBehavior
{
    public function __construct(
        private readonly Data $data = new KeyedDataObject(),
    ) {
    }

    protected function source(): Data
    {
        return $this->data;
    }
}
