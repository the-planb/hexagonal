<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Symfony\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

trait TryExecuteCommandTrait
{
    /**
     * @param callable(InputInterface, OutputInterface ): void
     */
    protected function tryExecute(InputInterface $input, OutputInterface $output, callable $callback): int
    {
        try {
            $callback($input, $output);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $exception = $e instanceof HandlerFailedException ? $e->getPrevious() : $e;
            new InputOutput($input, $output)->caution($exception->getMessage());

            return Command::FAILURE;
        }
    }
}
