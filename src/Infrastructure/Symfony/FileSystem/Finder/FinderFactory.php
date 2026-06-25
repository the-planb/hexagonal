<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Symfony\FileSystem\Finder;

use PlanB\Core\Path\Path;
use PlanB\Hexagonal\Core\FileSystem\FinderFactoryInterface;
use PlanB\Hexagonal\Core\FileSystem\FinderInterface;

final readonly class FinderFactory implements FinderFactoryInterface
{
    public function create(Path ...$paths): FinderInterface
    {
        $finder = new Finder();
        foreach ($paths as $path) {

            if (is_dir($path->path)) {
                $finder->in($path);
            } elseif (is_file($path->path)) {
                $finder->append($path);
            }

        }

        return $finder;
    }
}
