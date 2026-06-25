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
#[CoversClass(Adapter\YamlFile::class)]
final class YamlFileTest extends TestCase
{
    #[Test]
    public function it_supports_yaml_format(): void
    {
        $adapter = new Adapter\YamlFile();
        $this->assertTrue($adapter->supports(Format::YAML));
    }

    #[Test]
    public function it_parses_yaml_strings(): void
    {
        $adapter = new Adapter\YamlFile();
        $yaml = "parameters:\n  locale: es\n  environments:\n    - dev\n";
        $yaml .= "name:\n  nombre\n";
        $yaml .= "age:\n  23\n";

        $method = new \ReflectionMethod($adapter, 'parse');
        $result = $method->invoke($adapter, $yaml);

        $this->assertSame([
            'parameters' => ['locale' => 'es', 'environments' => ['dev']],
            'name' => 'nombre',
            'age' => 23,
        ], $result);
    }

    #[Test]
    public function it_formats_arrays_to_yaml_strings(): void
    {
        $adapter = new Adapter\YamlFile();
        $data = ['app' => ['debug' => true]];

        $method = new \ReflectionMethod($adapter, 'format');
        $result = $method->invoke($adapter, $data);

        $this->assertStringContainsString("app:\n  debug: true", $result);
    }
}
