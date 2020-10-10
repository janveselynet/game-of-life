<?php declare(strict_types = 1);

namespace Life\IO;

use DOMDocument;
use Life\Environment\Cell;
use Life\Environment\World;
use Life\TestCase;
use Tester\Assert;
use function file_exists;
use function unlink;

require_once __DIR__ . '/../../bootstrap.php';

final class XmlFileWriterTest extends TestCase
{
    private const OUTPUT_FILE = 'files/output.xml';
    private const EXPECTED_OUTPUT_FILE = '/files/output-expected.xml';

    public function testWritingXmlFile(): void
    {
        $world = $this->getSampleWorld();
        $writer = new XmlFileWriter($this->getFilePath(self::OUTPUT_FILE));

        $writer->saveWorld($world);

        $output = $this->loadXmlForComparison(self::OUTPUT_FILE);
        $expected = $this->loadXmlForComparison(self::EXPECTED_OUTPUT_FILE);
        Assert::same($expected, $output);
    }

    private function getSampleWorld(): World
    {
        $size = 5;
        $speciesCount = 3;

        $species = [
            [1,    null, 2,    null, 1   ],
            [null, null, null, null, null],
            [2,    null, 1,    null, 2   ],
            [null, null, null, null, null],
            [1,    null, 2,    null, 1   ],
        ];

        $cells = [];
        for ($y = 0; $y < $size; $y++) {
            $cells[$y] = [];
            for ($x = 0; $x < $size; $x++) {
                $cells[$y][$x] = new Cell($species[$y][$x]);
            }
        }

        return new World($size, $speciesCount, $cells);
    }

    /**
     */
    private function loadXmlForComparison(string $partialFilePath): string
    {
        $filePath = $this->getFilePath($partialFilePath);

        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->load($filePath);

        return (string)$dom->saveXML();
    }

    private function getFilePath(string $partialFilePath): string
    {
        return __DIR__ . '/' . $partialFilePath;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $outputFilePath = $this->getFilePath(self::OUTPUT_FILE);
        if (file_exists($outputFilePath)) {
            unlink($outputFilePath);
        }
    }
}

(new XmlFileWriterTest())->run();
