<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\FileSystem\Adapter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\Path\Path;
use PlanB\Hexagonal\Core\FileSystem\Exception\FileException;
use PlanB\Hexagonal\Infrastructure\Symfony\FileSystem\Adapter\JsonFile;

/**
 * @internal
 */
#[CoversClass(JsonFile::class)]
#[CoversClass(FileException::class)]
final class JsonFileTest extends TestCase
{
    private string $workspace;
    private JsonFile $jsonFile;

    protected function setUp(): void
    {
        $this->jsonFile = new JsonFile();
        $this->workspace = sys_get_temp_dir() . '/pb_json_adapter_test_' . uniqid();
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
    public function it_throws_unable_to_parse_exception_when_content_is_corrupt(): void
    {
        $path = Path::make("{$this->workspace}/corrupt.json");
        file_put_contents($path->path, '{ broken json ... ');

        $this->expectException(FileException::class);
        $this->expectExceptionMessageIsOrContains("Unable to parse file content at path: '{$path->path}'.");

        $this->jsonFile->read($path);
    }

    #[Test]
    public function it_throws_unable_to_format_exception_when_data_is_invalid(): void
    {
        $path = Path::make("{$this->workspace}/invalid.json");

        $resource = fopen('php://memory', 'r');
        $invalidData = ['stream' => $resource];

        $this->expectException(FileException::class);
        $this->expectExceptionMessageIsOrContains("Unable to format data for file at path: '{$path->path}'.");

        try {
            $this->jsonFile->write($path, $invalidData);
        } finally {
            fclose($resource);
        }
    }

    #[Test]
    public function test_it_formats_json_with_specific_flags_through_public_api(): void
    {
        $path = Path::make("{$this->workspace}/test.json");
        $data = [
            'url' => 'https://example.com',
            'text' => 'Mañana',
        ];

        $this->jsonFile->write($path, $data);

        $rawContent = file_get_contents($path->path);

        $this->assertStringContainsString("\n", $rawContent);
        $this->assertStringContainsString('https://example.com', $rawContent);
        $this->assertStringContainsString('Mañana', $rawContent);
    }
}
