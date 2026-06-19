<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\Pipeline;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Core\Pipeline\Context;
use PlanB\Hexagonal\Core\Pipeline\Pipeline;
use PlanB\Hexagonal\Core\Pipeline\ProcessorInterface;

/**
 * @internal
 */
#[CoversClass(Pipeline::class)]
final class PipelineTest extends TestCase
{
    public function test_it_delegates_processing_to_the_processor(): void
    {
        $context = new class(new \stdClass()) extends Context {};
        $pipes = [
            fn (Context $ctx) => $ctx,
            fn (Context $ctx) => $ctx,
        ];

        // Verificamos que el Pipeline invoca correctamente al procesador pasándole los argumentos intactos
        $processor = $this->createMock(ProcessorInterface::class);
        $processor->expects($this->once())
            ->method('process')
            ->with($context, ...$pipes)
            ->willReturn($context)
        ;

        $pipeline = new Pipeline($processor, $pipes);

        $result = $pipeline->process($context);
        $this->assertSame($context, $result);
    }

    public function test_it_delegates_processing_to_the_processor_when_invoke_directly(): void
    {
        $context = new class(new \stdClass()) extends Context {};
        $pipes = [
            fn (Context $ctx) => $ctx,
            fn (Context $ctx) => $ctx,
        ];

        // Verificamos que el Pipeline invoca correctamente al procesador pasándole los argumentos intactos
        $processor = $this->createMock(ProcessorInterface::class);
        $processor->expects($this->once())
            ->method('process')
            ->with($context, ...$pipes)
            ->willReturn($context)
        ;

        $pipeline = new Pipeline($processor, $pipes);

        $result = $pipeline($context);
        $this->assertSame($context, $result);
    }
}
