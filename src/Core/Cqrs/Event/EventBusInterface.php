<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\Cqrs\Event;

/**
 * @template TResult
 */
interface EventBusInterface
{
    public function publish(EventInterface ...$events): void;
}
