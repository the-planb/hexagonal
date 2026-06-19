<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\Pipeline;

/**
 * @template TPayload of mixed
 */
final class PipelineBuilder
{
    /** @var array<int, callable(Context<TPayload>): Context<TPayload>|PipeInterface<TPayload>> */
    private array $pipes = [];

    /** @var array<int, callable(Context<TPayload>): Context<TPayload>|PipeInterface<TPayload>> */
    private array $interceptors = [];

    /**
     * @param callable(Context<TPayload>): Context<TPayload>|PipeInterface<TPayload> $pipe
     *
     * @return static<TPayload>
     */
    public function pipe(callable|PipeInterface $pipe): self
    {
        $this->pipes[] = $pipe;

        return $this;
    }

    /**
     * @param callable(Context<TPayload>): Context<TPayload>|PipeInterface<TPayload> $interceptor
     *
     * @return static<TPayload>
     */
    public function intercept(callable|PipeInterface $interceptor): self
    {
        $this->interceptors[] = $interceptor;

        return $this;
    }

    /**
     * @param ProcessorInterface<TPayload> $processor
     *
     * @return Pipeline<TPayload>
     */
    public function build(?ProcessorInterface $processor = null): Pipeline
    {
        $processor ??= new DefaultProcessor();
        $pipeList = [];

        foreach ($this->pipes as $pipe) {
            $pipeList[] = $pipe;

            foreach ($this->interceptors as $interceptor) {
                $pipeList[] = $interceptor;
            }
        }

        return new Pipeline($processor, $pipeList);
    }
}
