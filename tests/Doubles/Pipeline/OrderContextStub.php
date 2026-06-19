<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Doubles\Pipeline;

use PlanB\Hexagonal\Core\Pipeline\Context;

/**
 * @extends Context<Order>
 */
final class OrderContextStub extends Context
{
    /** @var array<int, string> */
    public array $logTrace = [];
}
