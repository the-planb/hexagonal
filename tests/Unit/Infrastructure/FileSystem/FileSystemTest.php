<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\FileSystem;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\Path\Path;
use PlanB\Hexagonal\Core\FileSystem\Adapter\FileReader;
use PlanB\Hexagonal\Core\FileSystem\Adapter\FileWriter;
use PlanB\Hexagonal\Core\FileSystem\Directory;
use PlanB\Hexagonal\Core\FileSystem\Exception\FileException;
use PlanB\Hexagonal\Core\FileSystem\Exception\FileSystemIOException;
use PlanB\Hexagonal\Core\FileSystem\File;
use PlanB\Hexagonal\Core\FileSystem\FileSystem;
use PlanB\Hexagonal\Core\FileSystem\FinderFactoryInterface;
use PlanB\Hexagonal\Core\FileSystem\FinderInterface;
use PlanB\Hexagonal\Infrastructure\Symfony\FileSystem\Finder\FinderFactory;

/**
 * @internal
 */
#[CoversClass(FileSystem::class)]
#[CoversClass(FileSystemIOException::class)]
final class FileSystemTest extends TestCase
{
    private string $workspace;

    protected function setUp(): void
    {
        $this->workspace = sys_get_temp_dir() . '/pb_fs_test_' . uniqid();
        mkdir($this->workspace, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->cleanUpDirectory($this->workspace);
    }

    #[Test]
    public function it_delegates_reading_to_the_reader_locator(): void
    {
        $path = Path::make('/any/path.json');
        $expectedData = ['key' => 'value'];

        $reader = $this->createMock(FileReader::class);
        $reader->expects($this->once())
            ->method('read')
            ->with($path)
            ->willReturn($expectedData)
        ;

        $fileSystem = $this->createFileSystem(reader: $reader);
        $result = $fileSystem->read($path);

        $this->assertInstanceOf(File::class, $result);
        $this->assertSame($expectedData, $result->content);
    }

    #[Test]
    public function it_delegates_writing_to_the_writer_locator(): void
    {
        $path = Path::make('/any/path.json');
        $data = ['key' => 'value'];

        $writerLocator = $this->createMock(FileWriter::class);
        $writerLocator->expects($this->once())
            ->method('write')
            ->with($path, $data)
        ;

        $fileSystem = $this->createFileSystem(writer: $writerLocator);
        $fileSystem->write($path, $data);
    }

    #[Test]
    public function exists_returns_true_if_file_exists(): void
    {
        $filePath = $this->workspace . '/file.txt';
        touch($filePath);

        $fileSystem = $this->createFileSystem();
        $this->assertTrue($fileSystem->exists(Path::make($filePath)));
    }

    #[Test]
    public function exists_returns_false_if_file_does_not_exist(): void
    {
        $filePath = $this->workspace . '/non_existent.txt';

        $fileSystem = $this->createFileSystem();
        $this->assertFalse($fileSystem->exists(Path::make($filePath)));
    }

    #[Test]
    public function it_can_create_a_directory(): void
    {
        $dirPath = $this->workspace . '/new_sub_dir';

        $fileSystem = $this->createFileSystem();
        $fileSystem->mkdir(Path::make($dirPath));

        $this->assertDirectoryExists($dirPath);
    }

    #[Test]
    public function mkdir_does_nothing_if_directory_already_exists(): void
    {
        $dirPath = $this->workspace . '/existing_sub_dir';
        mkdir($dirPath, 0777, true);

        $fileSystem = $this->createFileSystem();
        $fileSystem->mkdir(Path::make($dirPath));

        $this->assertDirectoryExists($dirPath);
    }

    #[Test]
    public function mkdir_throws_exception_if_creation_fails(): void
    {
        $unwritableDir = $this->workspace . '/protected_dir';
        mkdir($unwritableDir, 0555, true);
        $targetPath = Path::make($unwritableDir . '/nested/dir');

        $this->expectException(FileSystemIOException::class);
        $this->expectExceptionMessage("Unable to create directory at path: '{$targetPath->path}'.");

        $fileSystem = $this->createFileSystem();
        $fileSystem->mkdir($targetPath);
    }

    #[Test]
    public function it_can_touch_and_create_a_new_empty_file(): void
    {
        $filePath = $this->workspace . '/touched_file.txt';

        $fileSystem = $this->createFileSystem();
        $fileSystem->touch(Path::make($filePath));

        $this->assertFileExists($filePath);
    }

    #[Test]
    public function touch_throws_exception_if_operation_fails(): void
    {
        $unwritableDir = $this->workspace . '/protected_dir';
        mkdir($unwritableDir, 0555, true);
        $targetPath = Path::make($unwritableDir . '/failed_touch.txt');

        $this->expectException(FileSystemIOException::class);
        $this->expectExceptionMessage("Unable to touch or create file at path: '{$targetPath->path}'.");

        $fileSystem = $this->createFileSystem();
        $fileSystem->touch($targetPath);
    }

    #[Test]
    public function remove_deletes_an_existing_file(): void
    {
        $filePath = $this->workspace . '/delete_me.txt';
        touch($filePath);

        $fileSystem = $this->createFileSystem();
        $fileSystem->remove(Path::make($filePath));

        $this->assertFileDoesNotExist($filePath);
    }

    #[Test]
    public function remove_deletes_a_directory_recursively(): void
    {
        $dirPath = $this->workspace . '/nested_dir/sub_dir';
        mkdir($dirPath, 0777, true);
        touch($dirPath . '/file1.txt');
        touch($dirPath . '/file2.txt');

        $fileSystem = $this->createFileSystem();
        $fileSystem->remove(Path::make($this->workspace . '/nested_dir'));

        $this->assertDirectoryDoesNotExist($this->workspace . '/nested_dir');
    }

    #[Test]
    public function remove_throws_exception_if_deletion_fails(): void
    {
        $dirPath = $this->workspace . '/undeletable_dir';
        mkdir($dirPath, 0777, true);
        $filePath = $dirPath . '/file.txt';
        touch($filePath);

        chmod($dirPath, 0555);

        $targetPath = Path::make($filePath);
        $this->expectException(FileSystemIOException::class);
        $this->expectExceptionMessage("Unable to remove the file or directory at path: '{$targetPath->path}'.");

        try {
            $fileSystem = $this->createFileSystem();
            $fileSystem->remove($targetPath);
        } finally {
            chmod($dirPath, 0777);
        }
    }

    #[Test]
    public function copy_duplicates_file_to_target_destination(): void
    {
        $sourcePath = $this->workspace . '/source.txt';
        $targetPath = $this->workspace . '/target.txt';
        file_put_contents($sourcePath, 'content');

        $fileSystem = $this->createFileSystem();
        $fileSystem->copy(Path::make($sourcePath), Path::make($targetPath));

        $this->assertFileExists($targetPath);
        $this->assertStringEqualsFile($targetPath, 'content');
    }

    #[Test]
    public function copy_throws_exception_if_source_file_does_not_exist(): void
    {
        $sourcePath = Path::make($this->workspace . '/ghost.txt');
        $targetPath = Path::make($this->workspace . '/destination.txt');

        $this->expectException(FileException::class);
        $this->expectExceptionMessage("File not found at path: '{$sourcePath->path}'.");

        $fileSystem = $this->createFileSystem();
        $fileSystem->copy($sourcePath, $targetPath);
    }

    #[Test]
    public function copy_throws_exception_if_target_is_not_writable(): void
    {
        $sourcePath = $this->workspace . '/source.txt';
        touch($sourcePath);

        $unwritableDir = $this->workspace . '/protected_dir';
        mkdir($unwritableDir, 0555, true);
        $targetPath = Path::make($unwritableDir . '/target.txt');

        $this->expectException(FileSystemIOException::class);
        $this->expectExceptionMessageIsOrContains("Unable to copy file from: '{$sourcePath}' to: '{$targetPath->path}'.");

        $fileSystem = $this->createFileSystem();
        $fileSystem->copy(Path::make($sourcePath), $targetPath);
    }

    #[Test]
    public function rename_moves_or_changes_file_name(): void
    {
        $sourcePath = $this->workspace . '/old_name.txt';
        $targetPath = $this->workspace . '/new_name.txt';
        touch($sourcePath);

        $fileSystem = $this->createFileSystem();
        $fileSystem->rename(Path::make($sourcePath), Path::make($targetPath));

        $this->assertFileDoesNotExist($sourcePath);
        $this->assertFileExists($targetPath);
    }

    #[Test]
    public function rename_throws_exception_if_source_file_does_not_exist(): void
    {
        $sourcePath = Path::make($this->workspace . '/ghost.txt');
        $targetPath = Path::make($this->workspace . '/destination.txt');

        $this->expectException(FileException::class);
        $this->expectExceptionMessageIsOrContains("File not found at path: '{$sourcePath->path}'.");

        $fileSystem = $this->createFileSystem();
        $fileSystem->rename($sourcePath, $targetPath);
    }

    #[Test]
    public function rename_throws_exception_if_target_is_invalid(): void
    {
        $sourcePath = $this->workspace . '/source.txt';
        touch($sourcePath);

        $unwritableDir = $this->workspace . '/protected_dir';
        mkdir($unwritableDir, 0555, true);
        $targetPath = Path::make($unwritableDir . '/moved.txt');

        $this->expectException(FileSystemIOException::class);
        $this->expectExceptionMessageIsOrContains("Unable to rename or move file from: '{$sourcePath}' to: '{$targetPath->path}'.");

        $fileSystem = $this->createFileSystem();
        $fileSystem->rename(Path::make($sourcePath), $targetPath);
    }

    #[Test]
    public function it_can_change_file_permissions_via_chmod(): void
    {
        $filePath = $this->workspace . '/permissions.txt';
        touch($filePath);

        $fileSystem = $this->createFileSystem();
        $fileSystem->chmod(Path::make($filePath), 0644);

        $actualPermissions = fileperms($filePath) & 0777;
        $this->assertSame(0644, $actualPermissions);
    }

    #[Test]
    public function chmod_throws_exception_if_file_does_not_exist(): void
    {
        $targetPath = Path::make($this->workspace . '/ghost.txt');

        $this->expectException(FileException::class);
        $this->expectExceptionMessage("File not found at path: '{$targetPath->path}'.");

        $fileSystem = $this->createFileSystem();
        $fileSystem->chmod($targetPath, 0755);
    }

    #[Test]
    public function chmod_throws_exception_if_operation_fails(): void
    {
        $filePath = $this->workspace . '/failed_chmod.txt';
        touch($filePath);

        $invalidPath = Path::make($filePath . '/invalid_sub_path');

        $targetPath = Path::make('/sys/class/net');

        // Evaluamos que nuestro manejador capture correctamente la excepción de Symfony
        if ($this->createFileSystem()->exists($targetPath)) {
            $this->expectException(FileSystemIOException::class);
            $this->expectExceptionMessageIsOrContains("Unable to change permissions (chmod) at path: '{$targetPath->path}'.");

            $this->createFileSystem()->chmod($targetPath, 0755);
        } else {
            // Respaldamos el flujo forzando un error de permisos local si el entorno restringe el acceso a /sys
            $this->expectException(FileSystemIOException::class);
            $this->expectExceptionMessageIsOrContains("Unable to change permissions (chmod) at path: '{$invalidPath->path}'.");

            $this->createFileSystem()->chmod($invalidPath, 0755);
        }
    }

    #[Test]
    public function it_delegates_finder_creation_to_the_factory(): void
    {
        $directory = Path::make('/dummy/path');
        $finderMock = $this->createStub(FinderInterface::class);

        $factoryMock = $this->createMock(FinderFactoryInterface::class);
        $factoryMock->expects($this->once())
            ->method('create')
            ->with($directory)
            ->willReturn($finderMock)
        ;

        $fileSystem = $this->createFileSystem(finder: $factoryMock);
        $result = $fileSystem->find($directory);

        $this->assertSame($finderMock, $result);
    }

    private function createFileSystem(?FileReader $reader = null, ?FileWriter $writer = null, ?FinderFactoryInterface $finder = null): FileSystem
    {
        return new FileSystem(
            $reader ?? $this->createStub(FileReader::class),
            $writer ?? $this->createStub(FileWriter::class),
            $finder ?? $this->createStub(FinderFactoryInterface::class),
        );
    }

    private function cleanUpDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "{$dir}/{$file}";
            if (is_dir($path)) {
                chmod($path, 0777);
                $this->cleanUpDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }
}
