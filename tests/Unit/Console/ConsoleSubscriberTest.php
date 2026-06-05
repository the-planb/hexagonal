<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Console;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Infrastructure\Console\ConsoleSubscriberTrait;
use PlanB\Hexagonal\Infrastructure\Console\SymfonyConsole;
use PlanB\Hexagonal\Tests\Doubles\Console\FakeConsoleSubscriber;
use PlanB\Hexagonal\Tests\Doubles\Event\FakeConsoleSubscriberBuilder;
use Symfony\Component\Console\ConsoleEvents;

/**
 * @internal
 */
#[CoversTrait(ConsoleSubscriberTrait::class)]
class ConsoleSubscriberTest extends TestCase
{
    private FakeConsoleSubscriberBuilder $builder;

    public function setUp(): void
    {
        $this->builder = new FakeConsoleSubscriberBuilder($this);
    }

    #[Test]
    public function it_can_be_instantiated(): void
    {
        $subscriber = $this->builder->build();
        self::assertInstanceOf(FakeConsoleSubscriber::class, $subscriber);
    }

    #[Test]
    public function it_has_the_console_events(): void
    {
        $subscriber = $this->builder->build();

        self::assertArraysHaveEqualValuesIgnoringOrder([
            ConsoleEvents::COMMAND => 'onConsoleCommand',
            ConsoleEvents::TERMINATE => 'onConsoleTerminate',
        ], $subscriber::getSubscribedEvents());
    }

    #[Test]
    public function it_initializes_without_console(): void
    {
        $subscriber = $this->builder->build();
        self::assertNull($subscriber->getConsole());
    }

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function it_creates_a_console_when_a_command_starts(): void
    {
        $subscriber = $this->builder
            ->passCommandEvent()
            ->build()
        ;
        self::assertInstanceOf(SymfonyConsole::class, $subscriber->getConsole());
    }

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function it_clears_the_console_when_a_command_terminates(): void
    {
        $subscriber = $this->builder
            ->passCommandEvent()
            ->passTerminateEvent()
            ->build()
        ;

        self::assertNull($subscriber->getConsole());
    }
}
