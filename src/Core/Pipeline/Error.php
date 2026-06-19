<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\Pipeline;

final class Error
{
    public function __construct(
        public private(set) int $code,
        public private(set) string $message,
    ) {}
}
