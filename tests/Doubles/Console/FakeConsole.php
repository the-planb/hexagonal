<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Doubles\Console;

use PlanB\Hexagonal\Infrastructure\Console\Emoji;
use PlanB\Hexagonal\Infrastructure\Console\SymfonyConsole;
use Symfony\Component\Console\Style\SymfonyStyle;

final class FakeConsole extends SymfonyConsole
{
    public function getIo(): SymfonyStyle
    {
        return $this->io;
    }

    public function getEmoji(): Emoji
    {
        return $this->emoji;
    }
}
