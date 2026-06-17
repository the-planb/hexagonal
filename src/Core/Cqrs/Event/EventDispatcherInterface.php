<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\Cqrs\Event;

interface EventDispatcherInterface
{
    public function dispatch(EventInterface $event): void;
}
