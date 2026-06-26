<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\FileSystem\Adapter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\Path\Path;
use PlanB\Hexagonal\Core\FileSystem\Adapter\DefaultAdapter;
use PlanB\Hexagonal\Core\FileSystem\Adapter\FileWriter;
use PlanB\Hexagonal\Core\FileSystem\Exception\FileException;
use PlanB\Hexagonal\Core\FileSystem\Exception\InvalidPathException;
use PlanB\Hexagonal\Infrastructure\Symfony\FileSystem\Adapter\JsonFile;

/**
 * @internal
 */
#[CoversClass(FileWriter::class)]
#[CoversClass(DefaultAdapter::class)]
#[CoversClass(JsonFile::class)]
#[CoversClass(FileException::class)]
#[CoversClass(InvalidPathException::class)]
final class FileWriterTest extends TestCase
{
    private string $workspace;
    private FileWriter $fileWriter;

    protected function setUp(): void
    {
        $this->fileWriter = new FileWriter([new DefaultAdapter(), new JsonFile()]);
        $this->workspace = sys_get_temp_dir() . '/pb_writer_test_' . uniqid();
        mkdir($this->workspace, 0777, true);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->workspace)) {
            $files = array_diff(scandir($this->workspace), ['.', '..']);
            foreach ($files as $file) {
                unlink("{$this->workspace}/{$file}");
            }
            rmdir($this->workspace);
        }
    }

    #[Test]
    public function it_resolves_and_writes_json_format_automatically_by_extension(): void
    {
        $path = Path::make("{$this->workspace}/output.json");
        $data = ['data' => 123];

        $this->fileWriter->write($path, $data);

        $this->assertFileExists($path->path);
        $this->assertJsonStringEqualsJsonString(
            json_encode($data),
            file_get_contents($path->path),
        );
    }

    #[Test]
    public function it_falls_back_to_plain_text_adapter_when_writing_unsupported_extension(): void
    {
        $path = Path::make("{$this->workspace}/raw_output.txt");
        $textContent = 1234;

        $this->fileWriter->write($path, $textContent);

        $this->assertFileExists($path->path);
        $this->assertSame((string) $textContent, file_get_contents($path->path));
    }

    #[Test]
    public function it_throws_not_writable_exception_when_file_put_contents_fails_physically(): void
    {
        $path = Path::make("{$this->workspace}/non_existent_folder/file.json");

        $this->expectException(FileException::class);
        $this->expectExceptionMessageIsOrContains("File or directory is not writable at path: '{$path->path}'.");

        $this->fileWriter->write($path, ['data' => 'test']);
    }

    #[Test]
    public function it_can_trigger_invalid_format_exception_manually(): void
    {
        // Este test fuerza la ejecución directa del método factoría en FileException para cubrir la línea descubierta
        $path = Path::make("{$this->workspace}/dummy.json");
        $exception = FileException::invalidFormat($path);

        $this->assertInstanceOf(FileException::class, $exception);
        $this->assertSame("The file content format is invalid at path: '{$path->path}'.", $exception->getMessage());
    }

    #[Test]
    public function it_throws_invalid_format_exception_when_has_not_a_custom_adpter(): void
    {
        $fileWriter = new FileWriter([]);
        $path = Path::make("{$this->workspace}/non_existent_folder/file.json");

        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessageIsOrContains("The path '{$path->path}' has an unknown extension.");

        $fileWriter->write($path, ['data' => 'test']);
    }
}
