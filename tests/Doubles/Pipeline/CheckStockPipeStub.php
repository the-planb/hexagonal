<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Doubles\Pipeline;

use PlanB\Hexagonal\Core\Pipeline\Context;
use PlanB\Hexagonal\Core\Pipeline\PipeInterface;

final class CheckStockPipeStub implements PipeInterface
{
    public function __invoke(Context $context): Context
    {
        /** @var OrderContextStub $context */
        if ($context->payload->stock <= 0) {
            $context->notification->addError('No queda stock disponible.');
        }

        return $context;
    }
}
