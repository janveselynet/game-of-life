<?php declare(strict_types=1);

use Life\Game;
use Life\IO\XmlFileReader;
use Life\IO\XmlFileWriter;
use Life\TestCase;
use Life\Random;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

final class IntegrationTest extends TestCase
{
    private const OUTPUT_FILE = 'output.xml';

    /**
     * @dataProvider xmlFilesWithWorldProvider
     * @param string $inputFile
     * @param string $expectedOutputFile
     */
    public function testGame(string $expectedOutputFile, string $inputFile): void
    {
        $random = new Random();
        $game = new Game(new Random());

        $input = new XmlFileReader($this->getFilePath($inputFile), $random);
        $output = new XmlFileWriter($this->getFilePath(self::OUTPUT_FILE));

        $game->run($input, $output);

        $output = $this->loadXmlForComparison(self::OUTPUT_FILE);
        $expected = $this->loadXmlForComparison($expectedOutputFile);

        Assert::same($expected, $output);
    }

    public function xmlFilesWithWorldProvider(): Generator
    {
        yield [
            'expectedOutputFile' => 'expected-output1.xml',
            'inputFile' => 'input1.xml',
        ];

        yield [
            'expectedOutputFile' => 'expected-output2.xml',
            'inputFile' => 'input2.xml',
        ];

        yield [
            'expectedOutputFile' => 'expected-output2.xml',
            'inputFile' => 'input3.xml',
        ];

        yield [
            'expectedOutputFile' => 'expected-output2.xml',
            'inputFile' => 'input4.xml',
        ];

        yield [
            'expectedOutputFile' => 'expected-output5.xml',
            'inputFile' => 'input5.xml',
        ];

        yield [
            'expectedOutputFile' => 'expected-output6.xml',
            'inputFile' => 'input6.xml',
        ];

        yield [
            'expectedOutputFile' => 'expected-output5.xml',
            'inputFile' => 'input7.xml',
        ];

        yield [
            'expectedOutputFile' => 'expected-output6.xml',
            'inputFile' => 'input8.xml',
        ];

        yield [
            'expectedOutputFile' => 'expected-output5.xml',
            'inputFile' => 'input9.xml',
        ];
    }

    private function loadXmlForComparison(string $partialFilePath): string
    {
        $filePath = $this->getFilePath($partialFilePath);

        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $dom->load($filePath);

        return $dom->saveXML();
    }

    private function getFilePath(string $partialFilePath): string
    {
        return __DIR__ . '/files/' . $partialFilePath;
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

(new IntegrationTest())->run();
