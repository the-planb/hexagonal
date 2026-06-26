<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Symfony\FileSystem\Adapter;

use PlanB\Hexagonal\Core\FileSystem\Adapter\FileAdapter;
use PlanB\Hexagonal\Core\FileSystem\Format;
use Symfony\Component\Yaml\Yaml;

final readonly class YamlFile extends FileAdapter
{
    public function supports(Format $format): bool
    {
        return $format === Format::YAML;
    }

    /**
     * @return mixed[]
     */
    protected function parse(string $content): array
    {
        $result = Yaml::parse($content);

        /** @var array<string, mixed> $response */
        $response = is_array($result) ? $result : [];

        return $response;
    }

    protected function format(mixed $data): string
    {
        return Yaml::dump(input: (array) $data, indent: 2, flags: Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE);
    }
}
