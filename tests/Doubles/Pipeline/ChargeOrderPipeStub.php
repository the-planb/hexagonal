<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Doubles\Pipeline;

use PlanB\Hexagonal\Core\Pipeline\Context;
use PlanB\Hexagonal\Core\Pipeline\PipeInterface;

final class ChargeOrderPipeStub implements PipeInterface
{
    public function __invoke(Context $context): Context
    {
        /** @var OrderContextStub $context */
        $context->payload->isPaid = true;

        return $context;
    }
}
