<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Symfony\Console;

final class InputOutputRegister
{
    private ?InputOutput $inputOutput = null;

    public function set(InputOutput $console): void
    {
        $this->inputOutput = $console;
    }

    public function get(): ?InputOutput
    {
        return $this->inputOutput;
    }

    public function clear(): self
    {
        $this->inputOutput = null;

        return $this;
    }
}
