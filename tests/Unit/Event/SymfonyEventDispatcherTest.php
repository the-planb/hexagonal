<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Infrastructure\Event\SymfonyEventDispatcher;
use PlanB\Hexagonal\Tests\Doubles\Event\FakeDomainEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

/**
 * @internal
 */
#[CoversClass(SymfonyEventDispatcher::class)]
class SymfonyEventDispatcherTest extends TestCase
{
    #[Test]
    public function it_dispatch_an_event(): void
    {
        $inner = $this->createMock(SymfonyEventDispatcherInterface::class);
        $inner
            ->expects(self::once())
            ->method('dispatch')
            ->with(
                self::isInstanceOf(FakeDomainEvent::class),
                self::equalTo(FakeDomainEvent::class),
            )
        ;

        $eventDispatcher = new SymfonyEventDispatcher($inner);
        $eventDispatcher->dispatch(new FakeDomainEvent());
    }

    #[Test]
    public function it_dispatch_an_event_wiht_a_custom_name(): void
    {
        $eventName = 'CustomName';
        $inner = $this->createMock(SymfonyEventDispatcherInterface::class);
        $inner
            ->expects(self::once())
            ->method('dispatch')
            ->with(
                self::isInstanceOf(FakeDomainEvent::class),
                self::equalTo($eventName),
            )
        ;

        $eventDispatcher = new SymfonyEventDispatcher($inner);
        $eventDispatcher->dispatch(new FakeDomainEvent(), $eventName);
    }
}
