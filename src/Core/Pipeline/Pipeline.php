<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\Pipeline;

/**
 * @template TPayload
 *
 * @implements PipeInterface<TPayload>
 */
final readonly class Pipeline implements PipeInterface
{
    /**
     * @param ProcessorInterface<TPayload> $processor
     * @param array<int, callable(Context<TPayload>): Context<TPayload>|PipeInterface<TPayload>> $pipes
     */
    public function __construct(
        private ProcessorInterface $processor,
        private array $pipes,
    ) {}

    /**
     * @param Context<TPayload> $context
     *
     * @return Context<TPayload>
     */
    public function __invoke(Context $context): Context
    {
        return $this->process($context);
    }

    /**
     * @param Context<TPayload>|TPayload $context *
     *
     * @return Context<TPayload>
     */
    public function process(mixed $context): Context
    {
        $context = $context instanceof Context ? $context : new Context($context);

        return $this->processor->process($context, ...$this->pipes);
    }
}
