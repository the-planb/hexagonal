<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\Console;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Infrastructure\Symfony\Console\ConsoleEventSubscriber;
use PlanB\Hexagonal\Infrastructure\Symfony\Console\InputOutput;
use PlanB\Hexagonal\Infrastructure\Symfony\Console\InputOutputRegister;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

/**
 * @internal
 */
#[CoversClass(ConsoleEventSubscriber::class)]
class ConsoleEventSubscriberTest extends TestCase
{
    private InputOutputRegister $provider;
    private ConsoleEventSubscriber $subscriber;

    public function setUp(): void
    {
        $this->provider = new InputOutputRegister();
        $this->subscriber = new ConsoleEventSubscriber($this->provider);
    }

    #[Test]
    public function it_has_the_console_events(): void
    {
        self::assertArraysHaveEqualValuesIgnoringOrder([
            ConsoleEvents::COMMAND => 'onConsoleCommand',
            ConsoleEvents::TERMINATE => 'onConsoleTerminate',
        ], $this->subscriber::getSubscribedEvents());
    }

    #[Test]
    public function it_initializes_without_console(): void
    {
        self::assertNull($this->provider->get());
    }

    #[Test]
    public function it_creates_a_console_when_a_command_starts(): void
    {
        $event = $this->createStub(ConsoleCommandEvent::class);
        $this->subscriber->onConsoleCommand($event);

        self::assertInstanceOf(InputOutput::class, $this->provider->get());
    }

    #[Test]
    public function it_clears_the_console_when_a_command_terminates(): void
    {
        $event = $this->createStub(ConsoleCommandEvent::class);
        $terminate = $this->createStub(ConsoleTerminateEvent::class);

        $this->subscriber->onConsoleCommand($event);
        $this->subscriber->onConsoleTerminate($terminate);

        self::assertNull($this->provider->get());
    }
}
