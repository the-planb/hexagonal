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
#[CoversClass(Adapter\CsvFile::class)]
final class CsvFileTest extends TestCase
{
    #[Test]
    public function it_supports_csv_format(): void
    {
        $adapter = new Adapter\CsvFile();
        $this->assertTrue($adapter->supports(Format::CSV));
    }

    #[Test]
    public function it_parses_empty_content_into_empty_array(): void
    {
        $adapter = new Adapter\CsvFile(separator: ';');
        $csv = '';

        $method = new \ReflectionMethod($adapter, 'parse');
        $result = $method->invoke($adapter, $csv);

        $this->assertSame([], $result);

        $csv = '   ';

        $method = new \ReflectionMethod($adapter, 'parse');
        $result = $method->invoke($adapter, $csv);

        $this->assertSame([], $result);
    }

    #[Test]
    public function it_parses_csv_content_into_indexed_arrays(): void
    {
        $adapter = new Adapter\CsvFile(separator: ';');
        $csv = "John;Doe;30\nJane;Doe;25";

        $method = new \ReflectionMethod($adapter, 'parse');
        $result = $method->invoke($adapter, $csv);

        $expected = [
            ['John', 'Doe', '30'],
            ['Jane', 'Doe', '25'],
        ];
        $this->assertSame($expected, $result);
    }

    #[Test]
    public function it_formats_bidimensional_arrays_into_csv_strings(): void
    {
        $adapter = new Adapter\CsvFile(separator: ',');
        $data = [
            ['a', 'b'],
            ['c', 'd'],
        ];

        $method = new \ReflectionMethod($adapter, 'format');
        $result = $method->invoke($adapter, $data);

        $this->assertSame("a,b\nc,d\n", $result);
    }

    #[Test]
    public function it_formats_a_simple_string_into_csv_strings(): void
    {
        $adapter = new Adapter\CsvFile(separator: ',');
        $data = 'value1,value2';

        $method = new \ReflectionMethod($adapter, 'format');
        $result = $method->invoke($adapter, $data);

        $this->assertEquals('value1,value2', trim((string) $result));
    }
}
