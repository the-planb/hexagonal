<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Symfony\Cqrs\Event;

use PlanB\Hexagonal\Core\Cqrs\Event\EventDispatcherInterface;
use PlanB\Hexagonal\Core\Cqrs\Event\EventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

class EventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private readonly SymfonyEventDispatcherInterface $dispatcher,
    ) {}

    public function dispatch(EventInterface $event, ?string $eventName = null): void
    {
        $eventName ??= $event::class;
        $this->dispatcher->dispatch($event, $eventName);
    }
}
