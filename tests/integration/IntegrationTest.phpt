<?php declare(strict_types=1);

use Life\Game;
use Life\IO\XmlFileReader;
use Life\IO\XmlFileWriter;
use Life\TestCase;
use Life\Utils\Random;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

final class IntegrationTest extends TestCase
{

    const OUTPUT_FILE = 'output.xml';

    protected function tearDown()
    {
        parent::tearDown();
        $outputFilePath = $this->getFilePath(self::OUTPUT_FILE);
        if (file_exists($outputFilePath)) {
            unlink($outputFilePath);
        }
    }

    /**
     * @param string $inputFile
     * @param string $expectedOutputFile
     * @dataProvider getInputAndExpectedOutputFiles
     */
    public function testGame(string $inputFile, string $expectedOutputFile)
    {
        $random = new Random();
        $game = new Game($random);
        $input = new XmlFileReader($this->getFilePath($inputFile), $random);
        $output = new XmlFileWriter($this->getFilePath(self::OUTPUT_FILE));
        $game->run($input, $output);
        $output = $this->loadXmlForComparison(self::OUTPUT_FILE);
        $expected = $this->loadXmlForComparison($expectedOutputFile);
        Assert::same($expected, $output, "Expected XML and output XML should be same");
    }

    /**
     * @return array
     */
    public function getInputAndExpectedOutputFiles()
    {
        return [
            ["input1.xml", "expected-output1.xml"],
            ["input2.xml", "expected-output2.xml"],
            ["input3.xml", "expected-output2.xml"],
            ["input4.xml", "expected-output2.xml"],

            ["input5.xml", "expected-output5.xml"],
            ["input6.xml", "expected-output6.xml"],
            ["input7.xml", "expected-output5.xml"],
            ["input8.xml", "expected-output6.xml"],
            ["input9.xml", "expected-output5.xml"],
        ];
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
        return __DIR__ . '/files/' . $partialFilePath;
    }

}

(new IntegrationTest())->run();
