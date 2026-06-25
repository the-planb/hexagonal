<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\FileSystem\Adapter;

use PlanB\Core\Path\Path;
use PlanB\Hexagonal\Core\FileSystem\Format;

interface FileWriterInterface
{
    public function supports(Format $format): bool;

    public function write(Path $path, mixed $data): void;
}
