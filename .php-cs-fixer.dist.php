<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = new Finder()
    ->in(__DIR__)
    ->path(['src', 'tests'])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
;

return new Config()
    ->setRules([
        '@PhpCsFixer' => true,
        '@PHP82Migration:risky' => true,
        'return_assignment' => false,
        'use_arrow_functions' => false,

        'php_unit_method_casing' => ['case' => 'snake_case'],

        'php_unit_test_class_requires_covers' => false,
        'php_unit_attributes' => [
            'keep_annotations' => false,
        ],

        'phpdoc_align' => [
            'align' => 'left',
        ],

        // Desactiva el estilo Yoda (ej: if (null === $var) -> if ($var === null))
        'yoda_style' => [
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],

        // Mantiene la concatenación con espacios: $a . ' ' . $b
        'concat_space' => ['spacing' => 'one'],
        // Evita que los comentarios multilínea se conviertan en una sola línea si no quieres
        'multiline_comment_opening_closing' => false,
        // Configuración de importaciones (alfabético y limpio)
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => ['class', 'function', 'const'],
        ],
        // Fuerza la coma final en arrays y parámetros multilínea (mejora los diffs de Git)
        'trailing_comma_in_multiline' => [
            'elements' => ['arrays', 'arguments', 'parameters'],
        ],
        // Elimina espacios antes del punto y coma (tu petición anterior)
        'no_singleline_whitespace_before_semicolons' => true,
        // Permite que PHPDoc sea más flexible con tipos simples
        'phpdoc_to_comment' => false,
        // No obliga a usar el operador de separación en números largos (ej: 1_000_000)
        'numeric_literal_separator' => false,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setUsingCache(true)
    ->setCacheFile(__DIR__ . '/var/cache/php-cs-fixer.cache')
;
