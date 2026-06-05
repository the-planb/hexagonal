<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Doubles\Console;

use PlanB\Hexagonal\Infrastructure\Console\ConsoleSubscriberTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FakeConsoleSubscriber implements EventSubscriberInterface
{
    use ConsoleSubscriberTrait;

    public static function getSubscribedEvents(): array
    {
        return [
            ...self::$events,
        ];
    }

    public function getConsole(): ?FakeConsole
    {
        return $this->console;
    }

    private function buildConsole(InputInterface $input, OutputInterface $output): FakeConsole
    {
        return new FakeConsole($input, $output);
    }
}
