<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\FileSystem\Exception;

use PlanB\Core\Path\Path;

final class InvalidPathException extends \InvalidArgumentException implements FileSystemException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function invalidFormat(Path $path): self
    {
        return new self("The path '{$path->path}' has an unknown extension.");
    }
}
