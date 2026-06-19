<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\Pipeline;

/**
 * @template TPayload
 */
interface PipeInterface
{
    /**
     * @param Context<TPayload> $context
     *
     * @return Context<TPayload>
     */
    public function __invoke(Context $context): Context;
}
