<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveParentDelegatingConstructorRector;
use Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withSets([
        LevelSetList::UP_TO_PHP_85, // Cambia esto a PHP_83 o PHP_84 si usas versiones más recientes
        SetList::CODE_QUALITY,      // Reglas para mejorar la legibilidad y simplicidad
        SetList::DEAD_CODE,         // Elimina variables, métodos y parámetros que no se usan
        SetList::EARLY_RETURN,      // Transforma estructuras complejas en retornos tempranos
    ])
    ->withSkip([
        RemoveNonExistingVarAnnotationRector::class,
    ])
    ->withTypeCoverageLevel(0);