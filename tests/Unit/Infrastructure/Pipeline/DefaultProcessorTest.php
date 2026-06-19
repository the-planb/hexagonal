<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\Pipeline;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Core\Pipeline\Context;
use PlanB\Hexagonal\Core\Pipeline\DefaultProcessor;
use PlanB\Hexagonal\Core\Pipeline\PipeStatus;

/**
 * @internal
 */
#[CoversClass(DefaultProcessor::class)]
final class DefaultProcessorTest extends TestCase
{
    private DefaultProcessor $processor;

    protected function setUp(): void
    {
        $this->processor = new DefaultProcessor();
    }

    #[Test]
    public function it_executes_all_pipes_and_marks_completed_when_no_errors_occur(): void
    {
        $context = new Context([]);

        $pipe1 = function (Context $ctx) {
            $ctx->payload[] = 'paso 1';

            return $ctx;
        };

        $pipe2 = function (Context $ctx) {
            $ctx->payload[] = 'paso 2';

            return $ctx;
        };

        $result = $this->processor->process($context, $pipe1, $pipe2);

        $this->assertSame(PipeStatus::COMPLETED, $result->status);
        $this->assertSame(['paso 1', 'paso 2'], $result->payload);
        $this->assertFalse($result->notification->hasErrors());
    }

    #[Test]
    public function it_stops_processing_and_marks_failed_when_a_pipe_adds_errors(): void
    {
        $context = new Context([]);

        $pipe1 = function (Context $ctx) {
            $ctx->payload[] = 'paso 1';

            return $ctx;
        };

        $failingPipe = function (Context $ctx) {
            $ctx->notification->addError('Mensaje de error');

            return $ctx;
        };

        $pipe3 = function (Context $ctx) {
            $ctx->payload[] = 'paso 3';

            return $ctx;
        };

        $result = $this->processor->process($context, $pipe1, $failingPipe, $pipe3);

        $this->assertSame(PipeStatus::FAILED, $result->status);
        // El paso 3 jamás debe ejecutarse porque el 'while' se detiene al cambiar el estado
        $this->assertSame(['paso 1'], $result->payload);
        $this->assertTrue($result->notification->hasErrors());
    }

    #[Test]
    public function it_does_not_execute_any_pipes_if_initial_status_is_not_processing(): void
    {
        $context = new Context([]);
        $context->status = PipeStatus::FAILED;

        $spyPipeCalled = false;
        $pipe = function (Context $ctx) use (&$spyPipeCalled) {
            $spyPipeCalled = true;

            return $ctx;
        };

        $result = $this->processor->process($context, $pipe);

        $this->assertSame(PipeStatus::FAILED, $result->status);
        // Al no cumplirse la condición del while desde el inicio, el espía nunca cambia a true
        $this->assertFalse($spyPipeCalled, 'El bucle while no debería haber comenzado si el estado no es PROCESSING.');
    }

    #[Test]
    public function it_stops_immediately_if_a_pipe_mutates_the_status_to_cancelled(): void
    {
        $context = new Context([]);

        $cancellingPipe = function (Context $ctx) {
            $ctx->status = PipeStatus::CANCELLED;
            $ctx->payload[] = 'paso 1';

            return $ctx;
        };

        $spyPipeCalled = false;
        $pipe2 = function (Context $ctx) use (&$spyPipeCalled) {
            $spyPipeCalled = true;

            return $ctx;
        };

        $result = $this->processor->process($context, $cancellingPipe, $pipe2);

        $this->assertSame(PipeStatus::CANCELLED, $result->status);
        $this->assertSame(['paso 1'], $result->payload);
        // Certifica que la condición dinámica del 'while' detecta el cambio de estado en la siguiente vuelta
        $this->assertFalse($spyPipeCalled, 'El bucle while debería haberse roto inmediatamente tras el cambio a CANCELLED.');
    }
}
