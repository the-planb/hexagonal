<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\FileSystem;

use PlanB\Core\Path\Path;

final class Directory
{
    public private(set) Path $path;

    public function __construct(Path|string $path)
    {
        $this->path = $path instanceof Path ? $path : Path::make($path);
    }

    public function equals(self $other): bool
    {
        return $this->path->equals($other->path);
    }
}
