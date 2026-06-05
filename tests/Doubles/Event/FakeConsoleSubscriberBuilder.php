<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Doubles\Event;

use PlanB\Hexagonal\Infrastructure\Testing\DoubleBuilder;
use PlanB\Hexagonal\Tests\Doubles\Console\FakeConsoleSubscriber;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

/**
 * @extends DoubleBuilder<FakeConsoleSubscriber>
 */
final class FakeConsoleSubscriberBuilder extends DoubleBuilder
{
    private FakeConsoleSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->subscriber = new FakeConsoleSubscriber();
    }

    public function passCommandEvent(): self
    {
        $event = $this->createMock(ConsoleCommandEvent::class);
        $this->subscriber->onConsoleCommand($event);

        return $this;
    }

    public function passTerminateEvent(): self
    {
        $event = $this->createMock(ConsoleTerminateEvent::class);
        $this->subscriber->onConsoleTerminate($event);

        return $this;
    }

    public function build(): FakeConsoleSubscriber
    {
        return $this->subscriber;
    }
}
