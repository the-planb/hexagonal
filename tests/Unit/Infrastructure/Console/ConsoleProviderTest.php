<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\Console;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Infrastructure\Symfony\Console\InputOutput;
use PlanB\Hexagonal\Infrastructure\Symfony\Console\InputOutputRegister;

/**
 * @internal
 */
#[CoversClass(InputOutputRegister::class)]
final class ConsoleProviderTest extends TestCase
{
    #[Test]
    public function it_initializes_with_no_console(): void
    {
        $provider = new InputOutputRegister();
        self::assertNull($provider->get());
    }

    #[Test]
    public function it_can_assign_a_console(): void
    {
        $console = $this->createStub(InputOutput::class);
        $provider = new InputOutputRegister();

        $provider->set($console);
        self::assertSame($console, $provider->get());
    }

    #[Test]
    public function it_can_clear_the_console(): void
    {
        $console = $this->createStub(InputOutput::class);
        $provider = new InputOutputRegister();

        $provider->set($console);
        $response = $provider->clear();

        self::assertInstanceOf(InputOutputRegister::class, $response);
        self::assertNull($provider->get());
    }

    #[Test]
    public function it_can_clear_the_console_even_if_does_not_exists(): void
    {
        $provider = new InputOutputRegister();
        $response = $provider->clear();

        self::assertInstanceOf(InputOutputRegister::class, $response);
        self::assertNull($provider->get());
    }
}
