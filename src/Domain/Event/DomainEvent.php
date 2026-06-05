<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Domain\Event;

abstract class DomainEvent
{
    public private(set) \DateTimeImmutable $occurredOn;

    public function __construct()
    {
        $this->occurredOn = new \DateTimeImmutable();
    }
}
