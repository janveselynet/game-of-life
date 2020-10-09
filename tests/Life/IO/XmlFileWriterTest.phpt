<?php declare(strict_types=1);

namespace Life\IO;

use Life\Environment\Cell;
use Life\Environment\World;
use Life\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

final class XmlFileWriterTest extends TestCase
{

    const OUTPUT_FILE = 'files/output.xml';
    const EXPECTED_OUTPUT_FILE = '/files/output-expected.xml';

    /** @var XmlFileWriter */
    private $writer;

    protected function setUp()
    {
        parent::setUp();
        $this->writer = new XmlFileWriter($this->getFilePath(self::OUTPUT_FILE));
    }

    protected function tearDown()
    {
        parent::tearDown();
        $outputFilePath = $this->getFilePath(self::OUTPUT_FILE);
        if (file_exists($outputFilePath)) {
            unlink($outputFilePath);
        }
    }

    public function testWritingXmlFile()
    {
        $world = $this->getSampleWorld();
        $this->writer->saveWorld($world);
        $output = $this->loadXmlForComparison(self::OUTPUT_FILE);
        $expected = $this->loadXmlForComparison(self::EXPECTED_OUTPUT_FILE);
        Assert::same($expected, $output, "Expected XML and output XML should be same");
    }

    /**
     * @return World
     */
    private function getSampleWorld()
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
     * @param string $partialFilePath
     * @return string
     */
    private function loadXmlForComparison($partialFilePath)
    {
        $filePath = $this->getFilePath($partialFilePath);
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->load($filePath);
        return $dom->saveXML();
    }

    /**
     * @param string $partialFilePath
     * @return string
     */
    private function getFilePath(string $partialFilePath)
    {
        return __DIR__ . '/' . $partialFilePath;
    }

}

(new XmlFileWriterTest())->run();
