<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Infrastructure\PhpVersion;

/**
 * @internal
 */
#[CoversClass(PhpVersion::class)]
final class PhpVersionTest extends TestCase
{
    #[Test]
    public function it_returns_minimum_required_version(): void
    {
        $phpVersion = new PhpVersion();

        $this->assertSame('8.5.6', $phpVersion->getMinimum());
    }

    #[Test]
    public function current_environment_is_supported(): void
    {
        $phpVersion = new PhpVersion();

        $this->assertTrue($phpVersion->isSupported());
    }
}
