<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\FileSystem\Adapter;

use PlanB\Core\Path\Path;
use PlanB\Hexagonal\Core\FileSystem\Exception\InvalidPathException;
use PlanB\Hexagonal\Core\FileSystem\Format;

final readonly class FileWriter
{
    /**
     * @param iterable<FileWriterInterface> $writers
     */
    public function __construct(
        private iterable $writers,
    ) {}

    public function write(Path $path, mixed $data): void
    {
        $format = Format::fromExtension($path->extension());

        foreach ($this->writers as $writer) {
            if ($writer->supports($format)) {
                $writer->write($path, $data);

                return;
            }
        }

        throw InvalidPathException::invalidFormat($path);
    }
}
