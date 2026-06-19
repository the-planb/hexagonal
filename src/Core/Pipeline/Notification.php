<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\Pipeline;

final class Notification
{
    /** @var array<int, Error> */
    public private(set) array $errors = [];

    public function addError(string $message, int $code = 100): void
    {
        $this->errors[] = new Error($code, $message);
    }

    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }
}
