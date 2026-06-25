<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\FileSystem;

use PlanB\Core\Path\Path;

/**
 * @extends \IteratorAggregate<int, Path>
 */
interface FinderInterface extends \IteratorAggregate
{
    public function in(Path ...$directories): self;

    public function append(Path ...$files): self;

    /**
     * @param array<string>|string $patterns
     */
    public function pattern(array|string $patterns): self;

    public function depth(int $depth): self;
}
