<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Integration\Pipeline;

use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Core\Pipeline\PipelineBuilder;
use PlanB\Hexagonal\Core\Pipeline\PipeStatus;
use PlanB\Hexagonal\Core\Pipeline\ProcessorInterface;
use PlanB\Hexagonal\Tests\Doubles\Pipeline\ChargeOrderPipeStub;
use PlanB\Hexagonal\Tests\Doubles\Pipeline\CheckStockPipeStub;
use PlanB\Hexagonal\Tests\Doubles\Pipeline\IntegrationTestProcessor;
use PlanB\Hexagonal\Tests\Doubles\Pipeline\Order;
use PlanB\Hexagonal\Tests\Doubles\Pipeline\OrderContextStub;
use PlanB\Hexagonal\Tests\Doubles\Pipeline\TraceInterceptorSpy;

// -----------------------------------------------------------------------------
// SUITE DE INTEGRACIÓN (DOCUMENTACIÓN EJECUTABLE)
// -----------------------------------------------------------------------------

/**
 * @internal
 */
final class PipelineIntegrationTest extends TestCase
{
    private ProcessorInterface $processor;

    protected function setUp(): void
    {
        $this->processor = new IntegrationTestProcessor();
    }

    /**
     * ESCENARIO 1: Camino feliz.
     * Todos los pipes e interceptores se ejecutan, el pedido se procesa y se completa correctamente.
     */
    public function test_successful_pipeline_flow(): void
    {
        $order = new Order(id: 100, price: 50, stock: 5);
        $context = new OrderContextStub($order);

        $pipeline = new PipelineBuilder()
            ->pipe(new CheckStockPipeStub())
            ->pipe(new ChargeOrderPipeStub())
            ->intercept(new TraceInterceptorSpy('logger'))
            ->build($this->processor)
        ;

        /** @var OrderContextStub $result */
        $result = $pipeline->process($context);

        // Verificaciones de estado y negocio
        $this->assertSame(PipeStatus::COMPLETED, $result->status);
        $this->assertTrue($result->payload->isPaid);
        $this->assertFalse($result->notification->hasErrors());

        // Verificación del entrelazado de interceptores
        $expectedTrace = ['executed_logger', 'executed_logger'];
        $this->assertSame($expectedTrace, $result->logTrace);
    }

    /**
     * ESCENARIO 2: Cortocircuito por error de validación.
     * Si un pipe registra fallos, el procesador aborta y los pipes posteriores no se ejecutan.
     */
    public function test_pipeline_short_circuits_on_error(): void
    {
        $order = new Order(id: 200, price: 20, stock: 0);
        $context = new OrderContextStub($order);

        $pipeline = new PipelineBuilder()
            ->pipe(new CheckStockPipeStub())
            ->pipe(new ChargeOrderPipeStub()) // No debería llegar a ejecutarse
            ->intercept(new TraceInterceptorSpy('logger'))
            ->build($this->processor)
        ;

        /** @var OrderContextStub $result */
        $result = $pipeline->process($context);

        $this->assertSame(PipeStatus::FAILED, $result->status);
        $this->assertTrue($result->notification->hasErrors());

        $this->assertFalse($result->payload->isPaid);

        $expectedTrace = [];
        $this->assertSame($expectedTrace, $result->logTrace);
    }
}
