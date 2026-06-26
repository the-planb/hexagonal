<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\FileSystem;

use PlanB\Core\Path\Path;

final readonly class File
{
    public private(set) Path $path;

    public function __construct(Path|string $path, public private(set) string|array $content)
    {
        $this->path = $path instanceof Path ? $path : Path::make($path);
    }

    public function equals(self $other): bool
    {
        return $this->path->equals($other->path);
    }
}
