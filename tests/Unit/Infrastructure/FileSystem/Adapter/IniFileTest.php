<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\FileSystem\Adapter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Hexagonal\Core\FileSystem\Format;
use PlanB\Hexagonal\Infrastructure\Symfony\FileSystem\Adapter;

/**
 * @internal
 */
#[CoversClass(Adapter\IniFile::class)]
final class IniFileTest extends TestCase
{
    #[Test]
    public function it_supports_ini_format(): void
    {
        $adapter = new Adapter\IniFile();
        $this->assertTrue($adapter->supports(Format::INI));
        $this->assertFalse($adapter->supports(Format::JSON));
    }

    #[Test]
    public function it_parses_ini_strings_into_arrays(): void
    {
        $adapter = new Adapter\IniFile();
        $ini = "[database]\nhost = 127.0.0.1\nport = 3306\nenabled = true";

        $method = new \ReflectionMethod($adapter, 'parse');
        $result = $method->invoke($adapter, $ini);

        $expected = [
            'database' => [
                'host' => '127.0.0.1',
                'port' => 3306,
                'enabled' => true,
            ],
        ];
        $this->assertSame($expected, $result);
    }

    #[Test]
    public function it_formats_arrays_into_ini_strings(): void
    {
        $adapter = new Adapter\IniFile();
        $data = [
            'database' => [
                'host' => '127.0.0.1',
                'enabled' => false,
                'port' => 3306,
            ],
        ];

        $method = new \ReflectionMethod($adapter, 'format');
        $result = $method->invoke($adapter, $data);

        $this->assertStringContainsString('[database]', $result);
        $this->assertStringContainsString('host = "127.0.0.1"', $result);
        $this->assertStringContainsString('enabled = false', $result);
        $this->assertStringContainsString('port = 3306', $result);
    }

    #[Test]
    public function it_formats_arrays_into_ini_strings_with_proper_structure_and_newlines(): void
    {
        $adapter = new Adapter\IniFile();
        $data = [
            'database' => [
                'host' => '127.0.0.1',
                'port' => 3306,
            ],
            'app' => [
                'debug' => true,
            ],
        ];

        $method = new \ReflectionMethod($adapter, 'format');
        $result = $method->invoke($adapter, $data);

        $expected = "[database]\nhost = \"127.0.0.1\"\nport = 3306\n[app]\ndebug = true";

        $this->assertSame($expected, $result);
    }

    #[Test]
    public function it_trims_surrounding_spaces_from_generated_ini(): void
    {
        $adapter = new Adapter\IniFile();
        $data = [
            'section' => [
                'key' => 'value',
            ],
        ];

        $method = new \ReflectionMethod($adapter, 'format');
        $result = $method->invoke($adapter, $data);

        $this->assertStringStartsNotWith("\n", $result);
        $this->assertStringEndsNotWith("\n", $result);
        $this->assertSame("[section]\nkey = \"value\"", $result);
    }

    #[Test]
    public function it_escapes_double_quotes_inside_strings(): void
    {
        $adapter = new Adapter\IniFile();
        $data = [
            'app' => [
                'description' => 'Un texto con "comillas" internas',
            ],
        ];

        $method = new \ReflectionMethod($adapter, 'format');
        $result = $method->invoke($adapter, $data);

        $this->assertStringContainsString('description = "Un texto con \"comillas\" internas"', $result);
    }
}
