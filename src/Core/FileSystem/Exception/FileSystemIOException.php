<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\FileSystem\Exception;

use PlanB\Core\Path\Path;

final class FileSystemIOException extends \RuntimeException implements FileSystemException
{
    public static function unableToRemove(Path $path): self
    {
        return new self("Unable to remove the file or directory at path: '{$path->path}'.");
    }

    public static function unableToCopy(Path $source, Path $target): self
    {
        return new self("Unable to copy file from: '{$source->path}' to: '{$target->path}'.");
    }

    public static function unableToRename(Path $source, Path $target): self
    {
        return new self("Unable to rename or move file from: '{$source->path}' to: '{$target->path}'.");
    }

    public static function unableToCreateDirectory(Path $path): self
    {
        return new self("Unable to create directory at path: '{$path->path}'.");
    }

    public static function unableToTouch(Path $path): self
    {
        return new self("Unable to touch or create file at path: '{$path->path}'.");
    }

    public static function unableToChangePermissions(Path $path): self
    {
        return new self("Unable to change permissions (chmod) at path: '{$path->path}'.");
    }
}
