<?php

declare(strict_types=1);

namespace Focus\Data\Tests;

use Focus\Data\Behavior\DataProxyBehavior;
use Focus\Data\DataProxy;

final readonly class ProxiesData extends DataProxy
{
    use DataProxyBehavior;
}
