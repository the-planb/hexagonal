<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Event;

use PlanB\Hexagonal\Domain\Event\DomainEvent;
use PlanB\Hexagonal\Domain\Event\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

class SymfonyEventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private readonly SymfonyEventDispatcherInterface $dispatcher,
    ) {}

    public function dispatch(DomainEvent $event, ?string $eventName = null): void
    {
        $eventName ??= $event::class;
        $this->dispatcher->dispatch($event, $eventName);
    }
}
