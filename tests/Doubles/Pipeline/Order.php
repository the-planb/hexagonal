<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Doubles\Pipeline;

final class Order
{
    public function __construct(
        public int $id,
        public int $price,
        public int $stock,
        public bool $isPaid = false,
    ) {}
}
