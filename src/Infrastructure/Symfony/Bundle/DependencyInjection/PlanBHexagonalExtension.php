<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Symfony\Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class PlanBHexagonalExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $path = __DIR__ . '/../../../../../config';
        $loader = new YamlFileLoader($container, new FileLocator($path));
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $path = __DIR__ . '/../../../../../config/packages';

        if (!is_dir($path)) {
            return;
        }

        $loader = new YamlFileLoader($container, new FileLocator($path));

        foreach (scandir($path) as $file) {
            if (in_array($file, ['.', '..'], true)) {
                continue;
            }

            if (preg_match('/\.ya?ml$/', $file)) {
                // El loader de Symfony se encarga de parsear el archivo y, al estar
                // en el método prepend(), inyecta la configuración en las extensiones correctas
                $loader->load($file);
            }
        }
    }
}
