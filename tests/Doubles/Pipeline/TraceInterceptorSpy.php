<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Doubles\Pipeline;

use PlanB\Hexagonal\Core\Pipeline\Context;
use PlanB\Hexagonal\Core\Pipeline\PipeInterface;

final readonly class TraceInterceptorSpy implements PipeInterface
{
    public function __construct(
        private string $identifier,
    ) {}

    public function __invoke(Context $context): Context
    {
        /** @var OrderContextStub $context */
        $context->logTrace[] = "executed_{$this->identifier}";

        return $context;
    }
}
