<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\Cqrs\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Core\Cqrs\Command\CommandInterface;
use PlanB\Hexagonal\Infrastructure\Symfony\Cqrs\Command\CommandBus;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @internal
 */
#[CoversClass(CommandBus::class)]
final class CommandBusTest extends TestCase
{
    #[Test]
    public function it_dispatch_a_commmand(): void
    {
        $command = $this->createStub(CommandInterface::class);
        $original = $this->createMock(MessageBusInterface::class);
        $commandBus = new CommandBus($original);

        $original
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo($command))
        ;

        $commandBus->dispatch($command);
    }
}
