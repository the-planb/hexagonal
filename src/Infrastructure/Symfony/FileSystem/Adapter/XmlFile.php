<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Infrastructure\Symfony\FileSystem\Adapter;

use PlanB\Hexagonal\Core\FileSystem\Adapter\FileAdapter;
use PlanB\Hexagonal\Core\FileSystem\Format;
use Sabre\Xml\ParseException;
use Sabre\Xml\Service as XmlService;

readonly class XmlFile extends FileAdapter
{
    private XmlService $xmlService;

    public function __construct()
    {
        $this->xmlService = new XmlService();
    }

    public function supports(Format $format): bool
    {
        return $format === Format::XML;
    }

    /**
     * @return mixed[]
     *
     * @throws ParseException
     */
    protected function parse(string $content): array
    {
        if (in_array(trim($content), ['', '0'], true)) {
            return [];
        }

        $result = $this->xmlService->parse($content);

        if (!is_array($result)) {
            return [];
        }

        return $result;
    }

    /**
     * @param mixed[]|object $data
     */
    protected function format(mixed $data): string
    {
        if (empty($data)) {
            return '<?xml version="1.0" encoding="UTF-8"?>';
        }

        /** @var string $rootName */
        $rootName = key($data);

        /** @var array<int|string, mixed>|object|string $rootContent */
        $rootContent = current($data);

        return $this->xmlService->write($rootName, $rootContent);
    }
}
