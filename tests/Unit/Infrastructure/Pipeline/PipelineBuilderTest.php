<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\Pipeline;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Core\Pipeline\Context;
use PlanB\Hexagonal\Core\Pipeline\DefaultProcessor;
use PlanB\Hexagonal\Core\Pipeline\PipelineBuilder;

/**
 * @internal
 */
#[CoversClass(PipelineBuilder::class)]
final class PipelineBuilderTest extends TestCase
{
    #[Test]
    public function it_manages_pipes_properly(): void
    {
        $context = new Context([]);

        $pipeline = new PipelineBuilder()
            ->pipe(function (Context $ctx) {
                $ctx->payload[] = 'pipe_1';

                return $ctx;
            })
            ->pipe(function (Context $ctx) {
                $ctx->payload[] = 'pipe_2';

                return $ctx;
            })
            ->build()
        ;

        $response = $pipeline->process($context);

        $this->assertSame([
            'pipe_1',
            'pipe_2',
        ], $response->payload);
    }

    #[Test]
    public function it_can_use_a_custom_processor(): void
    {
        $processor = $this->createMock(DefaultProcessor::class);
        $processor->expects($this->once())
            ->method('process')
            ->willReturn(new Context(['pipe_1', 'pipe_2']))
        ;

        $context = new Context([]);

        $pipeline = new PipelineBuilder()
            ->pipe(function (Context $ctx) {
                $ctx->payload[] = 'pipe_1';

                return $ctx;
            })
            ->pipe(function (Context $ctx) {
                $ctx->payload[] = 'pipe_2';

                return $ctx;
            })
            ->build($processor)
        ;

        $response = $pipeline->process($context);

        $this->assertSame([
            'pipe_1',
            'pipe_2',
        ], $response->payload);
    }

    #[Test]
    public function it_interleaves_interceptors_correctly_after_each_pipe(): void
    {
        $context = new Context([]);

        $pipeline = new PipelineBuilder()
            ->pipe(function (Context $ctx) {
                $ctx->payload[] = 'pipe_1';

                return $ctx;
            })
            ->pipe(function (Context $ctx) {
                $ctx->payload[] = 'pipe_2';

                return $ctx;
            })
            ->intercept(function (Context $ctx) {
                $ctx->payload[] = 'int_1';

                return $ctx;
            })
            ->intercept(function (Context $ctx) {
                $ctx->payload[] = 'int_2';

                return $ctx;
            })
            ->build()
        ;

        $response = $pipeline->process($context);

        $this->assertSame([
            'pipe_1',
            'int_1',
            'int_2',
            'pipe_2',
            'int_1',
            'int_2',
        ], $response->payload);
    }
}
