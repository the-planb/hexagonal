<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\Console;

use _PHPStan_2874a496b\Nette\Neon\Exception;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Infrastructure\Symfony\Console\TryExecuteCommandTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

/**
 * @internal
 */
#[CoversTrait(TryExecuteCommandTrait::class)]
final class TryExecuteCommandTraitTest extends TestCase
{
    use TryExecuteCommandTrait;

    public const string EXCEPTION_MESSAGE = 'asdadad';

    #[Test]
    #[DataProvider('exceptionsProvider')]
    public function it_returns_success_if_there_are_not_exceptions(\Exception $exception): void
    {
        $input = $this->createStub(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $output->expects($this->never())
            ->method('writeLn')
        ;

        $response = $this->tryExecute($input, $output, function ($input): void {});

        $this->assertEquals(Command::SUCCESS, $response);
    }

    #[Test]
    #[DataProvider('exceptionsProvider')]
    public function it_catch_exceptions(\Exception $exception): void
    {
        $input = $this->createStub(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $output->expects($this->atLeastOnce())
            ->method('writeLn')
            ->with($this->callback(fn (string $text) => str_contains($text, self::EXCEPTION_MESSAGE)))
        ;

        $response = $this->tryExecute($input, $output, function ($input) use ($exception): void {
            throw $exception;
        });

        $this->assertEquals(Command::FAILURE, $response);
    }

    public static function exceptionsProvider()
    {
        $envelope = self::createStub(Envelope::class);

        return [
            [new Exception(self::EXCEPTION_MESSAGE)],
            [new HandlerFailedException($envelope, [new Exception(self::EXCEPTION_MESSAGE)])],
        ];
    }
}
