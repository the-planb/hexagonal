<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\FileSystem;

use PlanB\Core\Path\Path;

interface FinderFactoryInterface
{
    public function create(Path ...$paths): FinderInterface;
}
