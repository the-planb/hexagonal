<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\FileSystem;

enum Format: string
{
    case JSON = 'json';
    case CSV = 'csv';
    case XML = 'xml';
    case YAML = 'yaml';
    case INI = 'ini';
    case TXT = 'txt';
    case DEFAULT = '';

    public static function fromExtension(string $extension): self
    {
        return match (strtolower($extension)) {
            'json' => self::JSON,
            'csv' => self::CSV,
            'xml' => self::XML,
            'yaml', 'yml' => self::YAML,
            'ini' => self::INI,
            'txt' => self::TXT,
            default => self::DEFAULT
        };
    }
}
