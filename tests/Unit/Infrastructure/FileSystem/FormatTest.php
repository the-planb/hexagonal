<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\FileSystem;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Core\FileSystem\Format;

/**
 * @internal
 */
#[CoversClass(Format::class)]
final class FormatTest extends TestCase
{
    #[Test]
    #[DataProvider('extensionProvider')]
    public function it_resolves_format_from_extension(string $extension, ?Format $expected): void
    {
        $this->assertSame($expected, Format::fromExtension($extension));
    }

    public static function extensionProvider(): array
    {
        return [
            ['json', Format::JSON],
            ['csv', Format::CSV],
            ['xml', Format::XML],
            ['yaml', Format::YAML],
            ['yml', Format::YAML],
            ['ini', Format::INI],
            ['txt', Format::TXT],
            ['md', Format::DEFAULT],
            ['JSON', Format::JSON],
            ['CSV', Format::CSV],
            ['XML', Format::XML],
            ['YAML', Format::YAML],
            ['YML', Format::YAML],
            ['INI', Format::INI],
            ['TXT', Format::TXT],
            ['MD', Format::DEFAULT],
            ['', Format::DEFAULT],
            ['bad ext', Format::DEFAULT],
        ];
    }
}
