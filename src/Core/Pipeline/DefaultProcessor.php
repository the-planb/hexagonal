<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\Pipeline;

/**
 * @template TPayload
 *
 * @implements ProcessorInterface<TPayload>
 */
final class DefaultProcessor implements ProcessorInterface
{
    /**
     * @param Context<TPayload> $context
     * @param callable(Context<TPayload>): Context<TPayload>|PipeInterface<TPayload> ...$pipes
     *
     * @return Context<TPayload>
     */
    public function process(Context $context, callable|PipeInterface ...$pipes): Context
    {
        $iterator = new \ArrayIterator($pipes);

        while ($iterator->valid() && $context->status === PipeStatus::PROCESSING) {
            $pipe = $iterator->current();
            $context = $pipe($context);

            if ($context->notification->hasErrors()) {
                $context->status = PipeStatus::FAILED;
            }

            $iterator->next();
        }

        if ($context->status === PipeStatus::PROCESSING) {
            $context->status = PipeStatus::COMPLETED;
        }

        return $context;
    }
}
