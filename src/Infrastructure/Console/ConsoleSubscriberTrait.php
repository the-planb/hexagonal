<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Console;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait ConsoleSubscriberTrait
{
    /**
     * @var array<string, callable|string>
     */
    public private(set) static array $events = [
        ConsoleEvents::COMMAND => 'onConsoleCommand',
        ConsoleEvents::TERMINATE => 'onConsoleTerminate',
    ];
    protected ?SymfonyConsole $console = null;

    final public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $this->console = $this->buildConsole($event->getInput(), $event->getOutput());
    }

    final public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        $this->console = null;
    }

    abstract private function buildConsole(InputInterface $input, OutputInterface $output): SymfonyConsole;
}
