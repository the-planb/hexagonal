<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Core\FileSystem\Adapter;

use PlanB\Hexagonal\Core\FileSystem\Format;

final readonly class DefaultAdapter extends FileAdapter
{
    public function supports(Format $format): bool
    {
        return $format === Format::TXT
            || $format === Format::DEFAULT;
    }

    protected function parse(string $content): mixed
    {
        return $content;
    }

    protected function format(mixed $data): string
    {
        return is_scalar($data) ? (string) $data : serialize($data);
    }
}
