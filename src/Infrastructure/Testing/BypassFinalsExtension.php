<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Testing;

use DG\BypassFinals;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

final class BypassFinalsExtension implements Extension
{
    public function bootstrap(
        Configuration $configuration,
        Facade $facade,
        ParameterCollection $parameters,
    ): void {
        BypassFinals::enable();
    }
}
