<?php declare(strict_types=1);

namespace Life\IO;

use DOMDocument;
use Life\Environment\Cell;
use Life\Environment\World;
use SimpleXMLElement;

final class XmlFileWriter implements IOutputWriter
{
    private const OUTPUT_TEMPLATE = 'files/output-template.xml';

    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function saveWorld(World $world): void
    {
        $size = $world->getSize();
        $cells = $world->getCells();

        $life = simplexml_load_file(sprintf('%s/%s', __DIR__, self::OUTPUT_TEMPLATE));
        $life->world->cells = $size;
        $life->world->species = $world->getSpeciesCount();

        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                /** @var Cell $cell */
                $cell = $cells[$y][$x];

                if ($cell->hasOrganism()) {
                    /** @var SimpleXMLElement $organism */
                    $organism = $life->organisms->addChild('organism');

                    $organism->addChild('x_pos', (string)$x);
                    $organism->addChild('y_pos', (string)$y);
                    $organism->addChild('species', (string)$cell->getOrganism());
                }
            }
        }

        $this->saveXml($life);
    }

    /**
     * @param SimpleXMLElement $life
     * @return void
     * @throws OutputWritingException
     */
    private function saveXml(SimpleXMLElement $life): void
    {
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($life->asXML());

        $result = file_put_contents($this->filePath, $dom->saveXML());
        if ($result === false) {
            throw new OutputWritingException('Writing XML file failed');
        }
    }

}
