<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\Cqrs\Command;

interface CommandBusInterface
{
    public function dispatch(CommandInterface $command): void;
}
