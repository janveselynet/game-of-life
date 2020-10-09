<?php declare(strict_types=1);

namespace Life\IO;

use Life\Environment\Cell;
use Life\Environment\World;
use Life\Exceptions\OutputWritingException;

/**
 * Class for saving given world to XML file
 */
final class XmlFileWriter implements IOutputWriter
{

    const OUTPUT_TEMPLATE = 'files/output-template.xml';

    /** @var string path of xml file to write to */
    private $filePath;

    /**
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @inheritdoc
     */
    public function saveWorld(World $world)
    {
        $life = simplexml_load_file(__DIR__ . '/' . self::OUTPUT_TEMPLATE);
        $size = $world->getSize();
        $life->world->cells = $size;
        $life->world->species = $world->getSpecies();
        $cells = $world->getCells();
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $cell = $cells[$y][$x]; /** @var Cell $cell */
                if ($cell->hasOrganism()) {
                    $organism = $life->organisms->addChild('organism'); /** @var \SimpleXMLElement $organism */
                    $organism->addChild('x_pos', $x);
                    $organism->addChild('y_pos', $y);
                    $organism->addChild('species', $cell->getOrganism());
                }
            }
        }
        $this->saveXml($life);
    }

    /**
     * @param \SimpleXMLElement $life
     * @return void
     * @throws OutputWritingException when writing output failed for some reason
     */
    private function saveXml(\SimpleXMLElement $life)
    {
        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($life->asXML());
        $result = file_put_contents($this->filePath, $dom->saveXML());
        if ($result === false) {
            throw new OutputWritingException("Writing XML file failed");
        }
    }

}
