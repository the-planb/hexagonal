<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Symfony\Cqrs\Command;

use PlanB\Hexagonal\Core\Cqrs\Command\CommandBusInterface;
use PlanB\Hexagonal\Core\Cqrs\Command\CommandInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CommandBus implements CommandBusInterface
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {}

    public function dispatch(CommandInterface $command): void
    {
        $this->commandBus->dispatch($command);
    }
}
