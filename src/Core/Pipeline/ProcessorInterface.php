<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\Pipeline;

/**
 * @template TPayload
 */
interface ProcessorInterface
{
    /**
     * @param Context<TPayload> $context
     * @param callable(Context<TPayload>): Context<TPayload>|PipeInterface<TPayload> ...$pipes
     *
     * @return Context<TPayload>
     */
    public function process(Context $context, callable|PipeInterface ...$pipes): Context;
}
