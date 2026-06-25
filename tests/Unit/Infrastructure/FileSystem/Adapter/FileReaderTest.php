<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\FileSystem\Adapter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\Path\Path;
use PlanB\Hexagonal\Core\FileSystem\Adapter\DefaultAdapter;
use PlanB\Hexagonal\Core\FileSystem\Adapter\FileReader;
use PlanB\Hexagonal\Core\FileSystem\Exception\FileException;
use PlanB\Hexagonal\Core\FileSystem\Exception\InvalidPathException;
use PlanB\Hexagonal\Infrastructure\Symfony\FileSystem\Adapter\JsonFile;

/**
 * @internal
 */
#[CoversClass(FileReader::class)]
#[CoversClass(DefaultAdapter::class)]
#[CoversClass(JsonFile::class)]
#[CoversClass(FileException::class)]
final class FileReaderTest extends TestCase
{
    private string $workspace;
    private FileReader $fileReader;

    protected function setUp(): void
    {
        $this->fileReader = new FileReader([new DefaultAdapter(), new JsonFile()]);
        $this->workspace = sys_get_temp_dir() . '/pb_reader_test_' . uniqid();
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
    public function it_resolves_and_reads_json_format_automatically_by_extension(): void
    {
        $path = Path::make("{$this->workspace}/file.json");
        file_put_contents($path->path, json_encode(['status' => 'ok']));

        $result = $this->fileReader->read($path);

        $this->assertSame(['status' => 'ok'], $result);
    }

    #[Test]
    public function it_falls_back_to_plain_text_adapter_when_extension_has_no_specific_adapter(): void
    {
        $path = Path::make("{$this->workspace}/log.txt");
        $rawText = 'Plain log content';
        file_put_contents($path->path, $rawText);

        $result = $this->fileReader->read($path);

        $this->assertSame($rawText, $result);
    }

    #[Test]
    public function it_throws_not_found_exception_when_file_does_not_exist(): void
    {
        $path = Path::make("{$this->workspace}/ghost_file.json");

        $this->expectException(FileException::class);
        $this->expectExceptionMessageIsOrContains("File not found at path: '{$path->path}'.");

        $this->fileReader->read($path);
    }

    #[Test]
    public function it_throws_not_readable_exception_when_file_has_no_read_permissions(): void
    {
        $path = Path::make("{$this->workspace}/unread.json");
        file_put_contents($path->path, '{"ok": true}');
        chmod($path->path, 0000);

        $this->expectException(FileException::class);
        $this->expectExceptionMessageIsOrContains("File is not readable at path: '{$path->path}'.");

        try {
            $this->fileReader->read($path);
        } finally {
            chmod($path->path, 0666);
        }
    }

    #[Test]
    public function it_throws_invalid_format_exception_when_has_not_a_custom_adpter(): void
    {
        $fileReader = new FileReader([]);
        $path = Path::make("{$this->workspace}/non_existent_folder/file.json");

        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessageIsOrContains("The path '{$path->path}' has an unknown extension.");

        $fileReader->read($path);
    }
}
