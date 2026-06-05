<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Console;

use PlanB\Hexagonal\Domain\Emoji;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class SymfonyConsole
{
    protected private(set) SymfonyStyle $io;
    protected private(set) Emoji $emoji;

    public function __construct(InputInterface $getInput, OutputInterface $getOutput)
    {
        $this->io = new SymfonyStyle($getInput, $getOutput);
        $this->emoji = new Emoji();
    }
}
