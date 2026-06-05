<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Domain\Event;

interface EventDispatcherInterface
{
    public function dispatch(DomainEvent $event): void;
}
