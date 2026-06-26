<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\FileSystem\Exception;

use PlanB\Core\Path\Path;

final class FileException extends \RuntimeException implements FileSystemException
{
    public static function notFound(Path $path): self
    {
        return new self("File not found at path: '{$path->path}'.");
    }

    public static function notReadable(Path $path): self
    {
        return new self("File is not readable at path: '{$path->path}'.");
    }

    public static function notWritable(Path $path): self
    {
        return new self("File or directory is not writable at path: '{$path->path}'.");
    }

    public static function invalidFormat(Path $path): self
    {
        return new self("The file content format is invalid at path: '{$path->path}'.");
    }

    // Nuevos métodos para añadir a FileException
    public static function unableToParse(Path $path): self
    {
        return new self("Unable to parse file content at path: '{$path->path}'.");
    }

    public static function unableToFormat(Path $path): self
    {
        return new self("Unable to format data for file at path: '{$path->path}'.");
    }
}
