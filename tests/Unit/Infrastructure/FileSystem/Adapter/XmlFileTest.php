<?php

declare(strict_types=1);

namespace PlanB\Hexagonal\Tests\Infrastructure\Symfony\FileSystem\Adapter;

use PHPUnit\Framework\TestCase;
use PlanB\Core\Path\Path;
use PlanB\Hexagonal\Core\FileSystem\Exception\FileException;
use PlanB\Hexagonal\Core\FileSystem\Format;
use PlanB\Hexagonal\Infrastructure\Symfony\FileSystem\Adapter\XmlFile;
use Sabre\Xml\Reader;
use Sabre\Xml\Service;

/**
 * @internal
 */
class XmlFileTest extends TestCase
{
    private XmlFile $adapter;
    private string $tempDir;

    protected function setUp(): void
    {
        $this->adapter = new XmlFile();
        $this->tempDir = sys_get_temp_dir() . '/xml_sabre_tests_' . uniqid();
        mkdir($this->tempDir, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->tempDir);
    }

    public function test_it_supports_xml_format_only(): void
    {
        $this->assertTrue($this->adapter->supports(Format::XML));
    }

    public function test_it_parses_valid_xml_with_flat_elements(): void
    {
        $xmlContent = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<root>
    <element>content</element>
</root>
XML;
        $filePath = $this->createTempFile('valid.xml', $xmlContent);

        $result = $this->adapter->read(Path::make($filePath));

        // Sabre/XML descompone el contenido del nodo de forma nativa
        $expected = [
            [
                'name' => '{}element',
                'value' => 'content',
                'attributes' => [],
            ],
        ];

        $this->assertSame($expected, $result);
    }

    public function test_it_throws_domain_exception_when_xml_is_corrupt(): void
    {
        $corruptXml = '<?xml version="1.0"?><root><unclosed-tag></root>';
        $filePath = $this->createTempFile('corrupt.xml', $corruptXml);

        $this->expectException(FileException::class);

        $this->adapter->read(Path::make($filePath));
    }

    public function test_it_returns_empty_array_on_empty_file(): void
    {
        $filePath = $this->createTempFile('empty.xml', '   ');

        $result = $this->adapter->read(Path::make($filePath));

        $this->assertSame([], $result);
    }

    public function test_it_formats_and_writes_scalar_values_maintaining_symmetry(): void
    {
        $filePath = $this->tempDir . '/output_scalar.xml';

        $data = [
            'catalog' => 5,
        ];

        $this->adapter->write(Path::make($filePath), $data);

        $this->assertFileExists($filePath);

        $content = file_get_contents($filePath);
        $this->assertStringContainsString('<catalog>5</catalog>', $content);
    }

    public function test_it_formats_complex_structures_with_sabre_conventions(): void
    {
        $filePath = $this->tempDir . '/output_complex.xml';
        $data = [
            'catalog' => [
                [
                    'name' => '{}book',
                    'value' => 'Clean Architecture',
                    'attributes' => ['id' => '123'],
                ],
            ],
        ];

        $this->adapter->write(Path::make($filePath), $data);

        $this->assertFileExists($filePath);

        $parsedData = $this->adapter->read(Path::make($filePath));

        $this->assertIsArray($parsedData);
    }

    public function test_it_formats_and_returns_default_xml_header_when_data_is_empty(): void
    {
        $filePath = $this->tempDir . '/empty_data_output.xml';

        $this->adapter->write(Path::make($filePath), []);

        $this->assertFileExists($filePath);
        $content = file_get_contents($filePath);

        $this->assertSame('<?xml version="1.0" encoding="UTF-8"?>', trim($content));
    }

    public function test_it_throws_domain_exception_when_formatting_fails_due_to_invalid_sabre_structure(): void
    {
        $filePath = $this->tempDir . '/failed_format.xml';

        $invalidSabreData = ['invalid', 'format'];

        $this->expectException(FileException::class);
        $this->adapter->write(Path::make($filePath), $invalidSabreData);
        $this->assertTrue(true);
    }

    public function test_it_returns_empty_array_when_xml_is_valid_but_sabre_returns_a_scalar(): void
    {
        $xmlContent = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<root>content</root>
XML;
        $filePath = $this->createTempFile('scalar_root.xml', $xmlContent);
        $reflection = new \ReflectionClass($this->adapter);
        $property = $reflection->getProperty('xmlService');

        /** @var Service $xmlService */
        $xmlService = $property->getValue($this->adapter);

        $xmlService->elementMap['{}root'] = function (Reader $reader) {
            $reader->next();

            return 'custom_scalar_string';
        };

        $result = $this->adapter->read(Path::make($filePath));

        $this->assertSame([], $result);
    }

    public function test_it_parses_multiple_sibling_elements_to_prevent_array_truncation_mutants(): void
    {
        $xmlContent = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<root>
    <first>value1</first>
    <second>value2</second>
    <third>value3</third>
</root>
XML;
        $filePath = $this->createTempFile('siblings.xml', $xmlContent);

        $result = $this->adapter->read(Path::make($filePath));

        $expected = [
            [
                'name' => '{}first',
                'value' => 'value1',
                'attributes' => [],
            ],
            [
                'name' => '{}second',
                'value' => 'value2',
                'attributes' => [],
            ],
            [
                'name' => '{}third',
                'value' => 'value3',
                'attributes' => [],
            ],
        ];

        $this->assertSame($expected, $result);
    }

    private function createTempFile(string $name, string $content): string
    {
        $path = $this->tempDir . '/' . $name;
        file_put_contents($path, $content);

        return $path;
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            unlink("{$dir}/{$file}");
        }
        rmdir($dir);
    }
}
