<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Symfony\FileSystem\Adapter;

use PlanB\Hexagonal\Core\FileSystem\Adapter\FileAdapter;
use PlanB\Hexagonal\Core\FileSystem\Format;

final readonly class CsvFile extends FileAdapter
{
    public function __construct(
        private string $separator = ',',
        private string $enclosure = '"',
        private string $escape = '\\',
    ) {}

    public function supports(Format $format): bool
    {
        return $format === Format::CSV;
    }

    /**
     * @return mixed[]
     */
    protected function parse(string $content): array
    {
        if (in_array(trim($content), ['', '0'], true)) {
            return [];
        }

        /** @var resource $stream */
        $stream = fopen('php://memory', 'r+');

        fwrite($stream, $content);
        rewind($stream);

        $rows = [];
        while (($row = fgetcsv($stream, 0, $this->separator, $this->enclosure, $this->escape)) !== false) {
            $rows[] = $row;
        }

        fclose($stream);

        return $rows;
    }

    protected function format(mixed $data): string
    {
        $data = is_string($data) ? $this->parse($data) : $data;

        /** @var resource $stream */
        $stream = fopen('php://memory', 'r+');

        /** @var array<null|bool|float|int|string> $data */
        foreach ($data as $row) {
            fputcsv($stream, (array) $row, $this->separator, $this->enclosure, $this->escape);
        }

        rewind($stream);
        $content = stream_get_contents($stream);
        fclose($stream);

        return $content ?: '';
    }
}
