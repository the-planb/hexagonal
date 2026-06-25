<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\FileSystem\Adapter;

use PlanB\Core\Path\Path;
use PlanB\Hexagonal\Core\FileSystem\Format;

interface FileReaderInterface
{
    public function supports(Format $format): bool;

    public function read(Path $path): mixed;
}
