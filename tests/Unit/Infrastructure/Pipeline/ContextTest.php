<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\Pipeline;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Core\Pipeline\Context;
use PlanB\Hexagonal\Core\Pipeline\Notification;
use PlanB\Hexagonal\Core\Pipeline\PipeStatus;

/**
 * @internal
 */
#[CoversClass(Context::class)]
final class ContextTest extends TestCase
{
    public function test_it_initializes_with_default_values(): void
    {
        $payload = new \stdClass();

        $context = new class($payload) extends Context {};

        $this->assertSame($payload, $context->payload);
        $this->assertSame(PipeStatus::PROCESSING, $context->status);
        $this->assertInstanceOf(Notification::class, $context->notification);
    }

    public function test_it_allows_mutating_status(): void
    {
        $context = new class(new \stdClass()) extends Context {};

        $this->assertSame(PipeStatus::CANCELLED, $context->cancel()->status);
        $this->assertFalse($context->notification->hasErrors());

        $this->assertSame(PipeStatus::FAILED, $context->fail('message')->status);
        $this->assertTrue($context->notification->hasErrors());
        $this->assertSame('message', $context->notification->errors[0]->message);
        $this->assertSame(100, $context->notification->errors[0]->code);
    }
}
