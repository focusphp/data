<?php

declare(strict_types=1);

namespace Focus\Data\Behavior;

use Focus\Data\Data;
use Focus\Data\KeyedData\KeyedDataObject;

/**
 * Provides simple source() method for a DataProxyMutable extension
 *
 * @see \Focus\Data\DataProxyMutable
 */
trait DataProxyMutableBehavior
{
    public function __construct(
        private Data $data = new KeyedDataObject(),
    ) {
    }

    protected function source(): Data
    {
        return $this->data;
    }
}
