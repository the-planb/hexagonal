<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\FileSystem\Adapter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\Path\Path;
use PlanB\Hexagonal\Core\FileSystem\Adapter\DefaultAdapter;
use PlanB\Hexagonal\Core\FileSystem\Format;

/**
 * @internal
 */
#[CoversClass(DefaultAdapter::class)]
final class DefaultAdapterTest extends TestCase
{
    private string $workspace;

    protected function setUp(): void
    {
        $this->workspace = sys_get_temp_dir() . '/pb_writer_test_' . uniqid();
        mkdir($this->workspace, 0777, true);
    }

    protected function tearDown(): void
    {
        //        if (is_dir($this->workspace)) {
        //            $files = array_diff(scandir($this->workspace), ['.', '..']);
        //            foreach ($files as $file) {
        //                unlink("{$this->workspace}/{$file}");
        //            }
        //            rmdir($this->workspace);
        //        }
    }

    #[Test]
    #[DataProvider('formatProvider')]
    public function it_supports_default_format(Format $format, bool $expected): void
    {
        $adapter = new DefaultAdapter();
        $this->assertEquals($expected, $adapter->supports($format));
    }

    public static function formatProvider(): array
    {
        return [
            [Format::DEFAULT, true],
            [Format::JSON, false],
            [Format::CSV, false],
            [Format::XML, false],
            [Format::YAML, false],
            [Format::INI, false],
            [Format::TXT, true],
        ];
    }

    #[Test]
    #[DataProvider('valuesProvider')]
    public function it_can_write_and_read_in_a_file(mixed $value, string $expected): void
    {
        $path = Path::make("{$this->workspace}/file");

        $adapter = new DefaultAdapter();
        $adapter->write($path, $value);
        $this->assertEquals($expected, $adapter->read($path));
    }

    public static function valuesProvider()
    {
        return [
            ['data', 'data'],
            [400, '400'],
            [['data'], serialize(['data'])],
        ];
    }
}
