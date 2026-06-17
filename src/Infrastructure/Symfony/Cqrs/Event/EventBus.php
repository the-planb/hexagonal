<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Symfony\Cqrs\Event;

use PlanB\Hexagonal\Core\Cqrs\Event\EventBusInterface;
use PlanB\Hexagonal\Core\Cqrs\Event\EventInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @template TResult
 *
 * @implements EventBusInterface<TResult>
 */
final readonly class EventBus implements EventBusInterface
{
    public function __construct(
        private MessageBusInterface $eventBus,
    ) {}

    public function publish(EventInterface ...$events): void
    {
        foreach ($events as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}
