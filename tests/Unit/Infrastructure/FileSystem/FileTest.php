<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\FileSystem;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\Path\Path;
use PlanB\Hexagonal\Core\FileSystem\File;

/**
 * @internal
 */
#[CoversClass(File::class)]
final class FileTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated_with_path_and_content(): void
    {
        $path = $this->createStub(Path::class);
        $content = 'file content';

        $file = new File($path, $content);

        $this->assertSame($path, $file->path);
        $this->assertSame($content, $file->content);
    }

    #[Test]
    public function it_checks_if_other_file_is_equals(): void
    {
        $fileA = new File('/path/to/file/a', '');
        $fileB = new File('/path/to/file/b', '');

        $target = new File('/path/to/file/a', 'other content');

        $this->assertTrue($target->equals($fileA));
        $this->assertFalse($target->equals($fileB));
    }
}
