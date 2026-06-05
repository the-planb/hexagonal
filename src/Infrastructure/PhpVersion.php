<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure;

final class PhpVersion
{
    private const string MINIMUM_VERSION = '8.5.6';

    public function getMinimum(): string
    {
        return self::MINIMUM_VERSION;
    }

    public function isSupported(): bool
    {
        return version_compare(PHP_VERSION, self::MINIMUM_VERSION, '>=');
    }
}
