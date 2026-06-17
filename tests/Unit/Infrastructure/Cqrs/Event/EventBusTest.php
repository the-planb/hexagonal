<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\Cqrs\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Core\Cqrs\Event\EventInterface;
use PlanB\Hexagonal\Infrastructure\Symfony\Cqrs\Event\EventBus;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @internal
 */
#[CoversClass(EventBus::class)]
final class EventBusTest extends TestCase
{
    #[Test]
    public function it_handle_a_event(): void
    {
        $commands = [
            $this->createStub(EventInterface::class),
            $this->createStub(EventInterface::class),
        ];

        $original = $this->createMock(MessageBusInterface::class);
        $commandBus = new EventBus($original);

        $original
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withParameterSetsInOrder(
                [$this->equalTo($commands[0])],
                [$this->equalTo($commands[1])],
            )
        ;

        $commandBus->publish(...$commands);
    }
}
