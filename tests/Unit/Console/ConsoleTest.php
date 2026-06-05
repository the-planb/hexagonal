<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Console;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Infrastructure\Console\Emoji;
use PlanB\Hexagonal\Infrastructure\Console\SymfonyConsole;
use PlanB\Hexagonal\Tests\Doubles\Console\FakeConsole;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @internal
 */
#[CoversClass(SymfonyConsole::class)]
class ConsoleTest extends TestCase
{
    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function it_can_be_instantiated(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $console = new FakeConsole($input, $output);

        self::assertInstanceOf(SymfonyStyle::class, $console->getIo());
        self::assertInstanceOf(Emoji::class, $console->getEmoji());
    }
}
