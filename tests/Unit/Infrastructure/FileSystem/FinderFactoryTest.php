<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\FileSystem;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\Path\Path;
use PlanB\Hexagonal\Infrastructure\Symfony\FileSystem\Finder\Finder;
use PlanB\Hexagonal\Infrastructure\Symfony\FileSystem\Finder\FinderFactory;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 */
#[CoversClass(FinderFactory::class)]
final class FinderFactoryTest extends TestCase
{
    #[Test]
    public function it_creates_a_symfony_finder_adapter_instance(): void
    {
        $factory = new FinderFactory();

        $tempDir = sys_get_temp_dir() . '/pb_factory_test_' . uniqid();
        new Filesystem()->mkdir([
            "{$tempDir}/uno",
            "{$tempDir}/dos",
        ]);

        new Filesystem()->touch([
            "{$tempDir}/uno/file1.txt",
            "{$tempDir}/uno/file2.md",
            "{$tempDir}/dos/file1.txt",
            "{$tempDir}/dos/file2.md",
        ]);

        $result = $factory->create(...[
            Path::make("{$tempDir}/uno"),
            Path::make("{$tempDir}/dos/file1.txt"),
            Path::make("{$tempDir}/dos/file2.md"),
        ])->pattern('*.txt');

        $values = array_map(fn(Path $path) => $path->path, iterator_to_array($result));

        $this->assertInstanceOf(Finder::class, $result);
        $this->assertArraysHaveEqualValuesIgnoringOrder([
            "{$tempDir}/dos/file1.txt",
            "{$tempDir}/uno/file1.txt"
        ], $values);


        new Filesystem()->remove($tempDir);

    }
}
