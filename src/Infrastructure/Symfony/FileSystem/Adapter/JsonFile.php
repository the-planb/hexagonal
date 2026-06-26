<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Symfony\FileSystem\Adapter;

use PlanB\Hexagonal\Core\FileSystem\Adapter\FileAdapter;
use PlanB\Hexagonal\Core\FileSystem\Format;

final readonly class JsonFile extends FileAdapter
{
    public function supports(Format $format): bool
    {
        return $format === Format::JSON;
    }

    /**
     * @return mixed[]
     *
     * @throws \JsonException
     */
    protected function parse(string $content): array
    {
        /** @var array<string, mixed> $response */
        $response = json_decode(json: $content, associative: true, flags: JSON_THROW_ON_ERROR);

        return $response;
    }

    protected function format(mixed $data): string
    {
        return json_encode(
            $data,
            JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        );
    }
}
