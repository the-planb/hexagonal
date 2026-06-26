<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Symfony\FileSystem\Finder;

use PlanB\Core\Path\Path;
use PlanB\Hexagonal\Core\FileSystem\Directory;
use PlanB\Hexagonal\Core\FileSystem\File;
use PlanB\Hexagonal\Core\FileSystem\FinderInterface;
use Symfony\Component\Finder\Finder as SymfonyFinder;

final  class Finder implements FinderInterface
{
    readonly private SymfonyFinder $finder;

    /** @var array<string, Path> */
    private array $appendedFiles;
    /** @var array<string> */
    private array $patterns;

    private bool $hasDirectories;

    public function __construct()
    {
        $this->finder = new SymfonyFinder();
        $this->finder->files();
        $this->appendedFiles = [];
        $this->patterns = [];
        $this->hasDirectories = false;
    }

    public function in(Path ...$directories): self
    {
        $this->hasDirectories = !empty($directories);

        foreach ($directories as $directory) {
            $this->finder->in($directory->path);
        }

        return $this;
    }

    public function append(Path ...$files): self
    {
        foreach ($files as $file) {
            $this->appendedFiles[$file->path] = $file;
        }

        return $this;
    }

    public function pattern(array|string $patterns): self
    {
        $patternsArray = (array)$patterns;

        foreach ($patternsArray as $pattern) {
            $this->finder->name($pattern);
            $this->patterns[] = $pattern;
        }

        return $this;
    }

    public function depth(int $depth): self
    {
        $this->finder->depth("<={$depth}");

        return $this;
    }

    /**
     * @return \Generator<int, Path>
     */
    public function getIterator(): \Generator
    {
        foreach ($this->appendedFiles as $file) {
            if ($this->matchesPatterns(basename($file->path))) {
                yield $file;
            }
        }
        if (!$this->hasDirectories) {
            return;
        }

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($this->finder as $file) {
            yield Path::make($file->getRealPath());
        }
    }

    private function matchesPatterns(string $filename): bool
    {
        if (empty($this->patterns)) {
            return true;
        }

        foreach ($this->patterns as $pattern) {
            if (fnmatch($pattern, $filename)) {
                return true;
            }
        }

        return false;
    }
}