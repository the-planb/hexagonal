<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\FileSystem\Adapter;

use PlanB\Core\Path\Path;
use PlanB\Hexagonal\Core\FileSystem\Exception\FileException;
use PlanB\Hexagonal\Core\FileSystem\Format;

abstract readonly class FileAdapter implements FileReaderInterface, FileWriterInterface
{
    abstract public function supports(Format $format): bool;

    public function read(Path $path): mixed
    {
        $resolvedPath = $path->path;

        if (!is_file($resolvedPath)) {
            throw FileException::notFound($path);
        }

        if (false === $content = @file_get_contents($resolvedPath)) {
            throw FileException::notReadable($path);
        }

        try {
            return $this->parse($content);
        } catch (\Throwable) {
            throw FileException::unableToParse($path);
        }
    }

    public function write(Path $path, mixed $data): void
    {
        $resolvedPath = $path->path;

        try {
            $content = $this->format($data);
        } catch (\Throwable) {
            throw FileException::unableToFormat($path);
        }

        $result = @file_put_contents($resolvedPath, $content);

        if ($result === false) {
            throw FileException::notWritable($path);
        }
    }

    abstract protected function parse(string $content): mixed;

    abstract protected function format(mixed $data): string;
}
