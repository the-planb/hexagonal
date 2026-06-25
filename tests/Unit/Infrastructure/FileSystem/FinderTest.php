<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Unit\Infrastructure\FileSystem;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\Path\Path;
use PlanB\Hexagonal\Core\FileSystem\File;
use PlanB\Hexagonal\Infrastructure\Symfony\FileSystem\Finder\Finder;

/**
 * @internal
 */
#[CoversClass(Finder::class)]
final class FinderTest extends TestCase
{
    private string $workspace;
    private string $workspace2;
    private Path $directory;
    private Path $directory2;

    protected function setUp(): void
    {
        // Primer directorio de trabajo
        $this->workspace = sys_get_temp_dir() . '/pb_finder_test_' . uniqid();
        mkdir($this->workspace, 0777, true);
        $this->directory = Path::make($this->workspace);

        file_put_contents("{$this->workspace}/file1.txt", 'content');
        file_put_contents("{$this->workspace}/file2.md", 'content');

        mkdir("{$this->workspace}/sub", 0777, true);
        file_put_contents("{$this->workspace}/sub/file3.txt", 'content');
        file_put_contents("{$this->workspace}/sub/file4.json", 'content');

        // Segundo directorio de trabajo (para testear múltiples directorios)
        $this->workspace2 = sys_get_temp_dir() . '/pb_finder_test2_' . uniqid();
        mkdir($this->workspace2, 0777, true);
        $this->directory2 = Path::make($this->workspace2);

        file_put_contents("{$this->workspace2}/file5.txt", 'content');
        file_put_contents("{$this->workspace2}/file6.json", 'content');
    }

    protected function tearDown(): void
    {
        $this->cleanUpDirectory($this->workspace);
        $this->cleanUpDirectory($this->workspace2);
    }

    #[Test]
    public function it_iterates_all_files_by_default(): void
    {
        $adapter = new Finder()
            ->in($this->directory);

        $results = iterator_to_array($adapter);

        $this->assertCount(4, $results);
        $this->assertContainsOnlyInstancesOf(Path::class, $results);
    }

    #[Test]
    public function it_filters_by_pattern_string(): void
    {
        $adapter = new Finder()
            ->in($this->directory);

        $adapter->pattern('*.txt');

        $results = array_map(fn(Path $path) => $path->path, iterator_to_array($adapter));

        $this->assertCount(2, $results);
        // Nota: asumo que implementas o usas assertEqualsCanonicalizing si no tienes la función personalizada del entorno
        $this->assertEqualsCanonicalizing([
            realpath("{$this->workspace}/file1.txt"),
            realpath("{$this->workspace}/sub/file3.txt"),
        ], $results);
    }

    #[Test]
    public function it_filters_by_multiple_patterns_array(): void
    {
        $adapter = new Finder()
            ->in($this->directory);
        $adapter->pattern(['*.txt', '*.md']);

        $results = iterator_to_array($adapter);

        $this->assertCount(3, $results); // file1.txt, file2.md, sub/file3.txt
    }

    #[Test]
    public function it_filters_by_depth(): void
    {
        $adapter = new Finder()
            ->in($this->directory);
        $adapter->depth(0); // Solo nivel raíz

        $results = iterator_to_array($adapter);

        $this->assertCount(2, $results); // file1.txt, file2.md
    }

    #[Test]
    public function it_allows_fluent_chaining(): void
    {
        $adapter = new Finder()
            ->in($this->directory);

        $fluent = $adapter->pattern('*.txt')->depth(0)->append();

        $this->assertSame($adapter, $fluent);
        $results = iterator_to_array($fluent);
        $this->assertCount(1, $results); // Solo file1.txt
    }

    #[Test]
    public function it_finds_files_in_multiple_directories_at_once(): void
    {
        $adapter = new Finder()
            ->in($this->directory, $this->directory2)
            ->pattern('*.txt');

        $results = array_map(fn(Path $path) => $path->path, iterator_to_array($adapter));

        $this->assertCount(3, $results); // file1.txt y sub/file3.txt (dir1) + file5.txt (dir2)
        $this->assertEqualsCanonicalizing([
            realpath("{$this->workspace}/file1.txt"),
            realpath("{$this->workspace}/sub/file3.txt"),
            realpath("{$this->workspace2}/file5.txt"),
        ], $results);
    }

    #[Test]
    public function it_appends_individual_files_and_applies_pattern_filters(): void
    {
        $fileTxt = Path::make("{$this->workspace2}/file5.txt");
        $fileJson = Path::make("{$this->workspace2}/file6.json");

        $adapter = new Finder()
            ->in($this->directory)
            ->append($fileTxt, $fileJson)
            ->pattern('*.txt'); // El patrón debe aplicar tanto a los directorios como a los appends

        $results = array_map(fn(Path $path) => $path->path, iterator_to_array($adapter));

        // Debería incluir los *.txt de 'directory' más '$fileTxt'. '$fileJson' se cae por patrón.
        $this->assertCount(3, $results);
        $this->assertEqualsCanonicalizing([
            realpath("{$this->workspace}/file1.txt"),
            realpath("{$this->workspace}/sub/file3.txt"),
            realpath("{$this->workspace2}/file5.txt"),
        ], $results);
    }

    #[Test]
    public function it_iterates_only_appended_files_when_no_directories_are_provided(): void
    {
        // Caso línea 82: (!$this->hasDirectories) -> return;
        $fileTxt = Path::make("{$this->workspace}/file1.txt");
        $fileMd = Path::make("{$this->workspace}/file2.md");

        $adapter = new Finder()
            ->append($fileTxt, $fileMd)
            ->pattern('*.txt'); // El patrón descarta file2.md

        $results = array_map(fn(Path $path) => $path->path, iterator_to_array($adapter));

        $this->assertCount(1, $results);
        $this->assertEquals([realpath("{$this->workspace}/file1.txt")], $results);
    }

    #[Test]
    public function it_does_not_filter_appended_files_when_no_patterns_are_defined(): void
    {
        // Caso línea 94: (empty($this->patterns)) -> return true;
        $fileTxt = Path::make("{$this->workspace}/file1.txt");
        $fileJson = Path::make("{$this->workspace}/sub/file4.json");

        $adapter = new Finder()
            ->append($fileTxt, $fileJson); // No invocamos al método ->pattern()

        $results = array_map(fn(Path $path) => $path->path, iterator_to_array($adapter));

        $this->assertCount(2, $results);
        $this->assertEqualsCanonicalizing([
            realpath("{$this->workspace}/file1.txt"),
            realpath("{$this->workspace}/sub/file4.json"),
        ], $results);
    }

    private function cleanUpDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );
        foreach ($files as $file) {
            $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
        }
        rmdir($dir);
    }
}