<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Domain\Event\DomainEvent;
use PlanB\Hexagonal\Tests\Doubles\Event\FakeDomainEvent;

/**
 * @internal
 */
#[CoversClass(DomainEvent::class)]
class DomainEventTest extends TestCase
{
    #[Test]
    public function test_can_be_instantiated(): void
    {
        $event = new FakeDomainEvent();
        self::assertInstanceOf(DomainEvent::class, $event);
    }

    #[Test]
    public function it_initializes_the_occurred_on_property_with_the_current_date(): void
    {
        $event = new FakeDomainEvent();

        self::assertEqualsWithDelta(
            new \DateTimeImmutable(),
            $event->occurredOn,
            1.0,
        );
    }
}
