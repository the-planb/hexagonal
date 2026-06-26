<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\FileSystem\Adapter;

use PlanB\Core\Path\Path;
use PlanB\Hexagonal\Core\FileSystem\Exception\InvalidPathException;
use PlanB\Hexagonal\Core\FileSystem\Format;

final readonly class FileReader
{
    /**
     * @param iterable<FileReaderInterface> $readers
     */
    public function __construct(
        private iterable $readers,
    ) {}

    public function read(Path $path): mixed
    {
        $format = Format::fromExtension($path->extension());

        foreach ($this->readers as $reader) {
            if ($reader->supports($format)) {
                return $reader->read($path);
            }
        }

        throw InvalidPathException::invalidFormat($path);
    }
}
