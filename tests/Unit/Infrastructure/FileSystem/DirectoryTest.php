<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\FileSystem;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\Path\Path;
use PlanB\Hexagonal\Core\FileSystem\Directory;

/**
 * @internal
 */
#[CoversClass(Directory::class)]
final class DirectoryTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated_if_path_has_no_extension(): void
    {
        $path = $this->createStub(Path::class);
        $path->method('hasExtension')->willReturn(false);

        $directory = new Directory($path);

        $this->assertSame($path, $directory->path);
    }

    #[Test]
    public function it_checks_if_other_file_is_equals(): void
    {
        $fileA = new Directory('/path/to/file/a', '');
        $fileB = new Directory('/path/to/file/b', '');

        $target = new Directory('/path/to/file/a', 'other content');

        $this->assertTrue($target->equals($fileA));
        $this->assertFalse($target->equals($fileB));
    }
}
