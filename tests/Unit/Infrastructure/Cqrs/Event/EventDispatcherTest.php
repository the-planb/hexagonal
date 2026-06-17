<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\Cqrs\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Core\Cqrs\Event\EventInterface;
use PlanB\Hexagonal\Infrastructure\Symfony\Cqrs\Event\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

/**
 * @internal
 */
#[CoversClass(EventDispatcher::class)]
class EventDispatcherTest extends TestCase
{
    #[Test]
    public function it_dispatch_an_event(): void
    {
        $event = $this->createStub(EventInterface::class);

        $inner = $this->createMock(SymfonyEventDispatcherInterface::class);
        $inner
            ->expects(self::once())
            ->method('dispatch')
            ->with(
                self::isInstanceOf($event::class),
                self::equalTo($event::class),
            )
        ;

        $eventDispatcher = new EventDispatcher($inner);
        $eventDispatcher->dispatch($event);
    }

    #[Test]
    public function it_dispatch_an_event_wiht_a_custom_name(): void
    {
        $event = $this->createStub(EventInterface::class);

        $eventName = 'CustomName';
        $inner = $this->createMock(SymfonyEventDispatcherInterface::class);
        $inner
            ->expects(self::once())
            ->method('dispatch')
            ->with(
                self::isInstanceOf($event::class),
                self::equalTo($eventName),
            )
        ;

        $eventDispatcher = new EventDispatcher($inner);
        $eventDispatcher->dispatch($event, $eventName);
    }
}
