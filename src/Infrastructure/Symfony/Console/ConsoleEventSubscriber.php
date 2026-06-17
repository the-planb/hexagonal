<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Symfony\Console;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class ConsoleEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private private(set) InputOutputRegister $register,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => 'onConsoleCommand',
            ConsoleEvents::TERMINATE => 'onConsoleTerminate',
        ];
    }

    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $console = new InputOutput($event->getInput(), $event->getOutput());

        $this->register->set($console);
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        $this->register->clear();
    }
}
