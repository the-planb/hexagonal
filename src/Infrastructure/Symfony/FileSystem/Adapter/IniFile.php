<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Symfony\FileSystem\Adapter;

use PlanB\Hexagonal\Core\FileSystem\Adapter\FileAdapter;
use PlanB\Hexagonal\Core\FileSystem\Format;

final readonly class IniFile extends FileAdapter
{
    public function supports(Format $format): bool
    {
        return $format === Format::INI;
    }

    /**
     * @return mixed[]
     */
    protected function parse(string $content): array
    {
        $result = parse_ini_string($content, true, INI_SCANNER_TYPED);

        return $result ?: [];
    }

    protected function format(mixed $data): string
    {
        return $this->buildIniString((array) $data);
    }

    /**
     * @param mixed[] $data
     */
    private function buildIniString(array $data): string
    {
        $content = '';
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $content .= "\n[{$key}]\n" . $this->buildIniString($value);
            } elseif (is_scalar($value)) {
                $content .= "{$key} = " . $this->exportValue($value) . "\n";
            }
        }

        return trim($content);
    }

    private function exportValue(bool|float|int|string $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_numeric($value)) {
            return (string) $value;
        }

        return '"' . str_replace('"', '\"', $value) . '"';
    }
}
