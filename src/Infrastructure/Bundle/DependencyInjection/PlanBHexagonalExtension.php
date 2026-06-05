<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class PlanBHexagonalExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $path = __DIR__ . '/../../../../config';

        $loader = new YamlFileLoader($container, new FileLocator($path));

        $loader->load('services.yaml');
    }
}
