<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\FileSystem;

use PlanB\Core\Path\Path;
use PlanB\Hexagonal\Core\FileSystem\Adapter\FileReader;
use PlanB\Hexagonal\Core\FileSystem\Adapter\FileWriter;
use PlanB\Hexagonal\Core\FileSystem\Exception\FileException;
use PlanB\Hexagonal\Core\FileSystem\Exception\FileSystemIOException;
use Symfony\Component\Filesystem\Exception\IOException as SymfonyIOException;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

final readonly class FileSystem
{
    private SymfonyFilesystem $symfonyFilesystem;

    public function __construct(
        private FileReader             $reader,
        private FileWriter             $writer,
        private FinderFactoryInterface $finderFactory,
    )
    {
        $this->symfonyFilesystem = new SymfonyFilesystem();
    }

    public function find(Path ...$paths): FinderInterface
    {
        return $this->finderFactory->create(...$paths);
    }

    public function read(Path $path): File
    {
        $content = $this->reader->read($path);
        return new File($path, $content);
    }

    public function write(Path $path, mixed $data): void
    {
        $this->writer->write($path, $data);
    }

    public function exists(Path $path): bool
    {
        return $this->symfonyFilesystem->exists($path->path);
    }

    public function remove(Path $path): void
    {
        try {
            $this->symfonyFilesystem->remove($path->path);
        } catch (SymfonyIOException) {
            throw FileSystemIOException::unableToRemove($path);
        }
    }

    public function mkdir(Path $path, int $mode = 0777): void
    {
        try {
            $this->symfonyFilesystem->mkdir($path->path, $mode);
        } catch (SymfonyIOException) {
            throw FileSystemIOException::unableToCreateDirectory($path);
        }
    }

    public function touch(Path $path): void
    {
        try {
            $this->symfonyFilesystem->touch($path->path);
        } catch (SymfonyIOException) {
            throw FileSystemIOException::unableToTouch($path);
        }
    }

    public function copy(Path $source, Path $target): void
    {
        if (!$this->exists($source)) {
            throw FileException::notFound($source);
        }

        try {
            $this->symfonyFilesystem->copy($source->path, $target->path, true);
        } catch (SymfonyIOException) {
            throw FileSystemIOException::unableToCopy($source, $target);
        }
    }

    public function rename(Path $source, Path $target): void
    {
        if (!$this->exists($source)) {
            throw FileException::notFound($source);
        }

        try {
            $this->symfonyFilesystem->rename($source->path, $target->path, true);
        } catch (SymfonyIOException) {
            throw FileSystemIOException::unableToRename($source, $target);
        }
    }

    public function chmod(Path $path, int $mode): void
    {
        if (!$this->exists($path)) {
            throw FileException::notFound($path);
        }

        try {
            $this->symfonyFilesystem->chmod($path->path, $mode);
        } catch (SymfonyIOException) {
            throw FileSystemIOException::unableToChangePermissions($path);
        }
    }
}
