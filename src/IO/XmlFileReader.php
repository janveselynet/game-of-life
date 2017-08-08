<?php

namespace Life\IO;

use Life\Environment\Cell;
use Life\Environment\World;
use Life\Exceptions\InvalidInputException;

/**
 * For reading and parsing XML file with input for Game of Life
 */
class XmlFileReader implements IInputReader
{

    /** @var string path of xml file to read from */
    private $filePath;

    /** @var World read initial world */
    private $initialWorld;

    /** @var int read iterations count */
    private $iterationsCount;

    /** @var bool if xml file was already read and parsed or not */
    private $fileLoaded;

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
    public function getInitialWorld(): World
    {
        $this->loadFile();
        return $this->initialWorld;
    }

    /**
     * @inheritdoc
     */
    public function getIterationsCount(): int
    {
        $this->loadFile();
        return $this->iterationsCount;
    }

    /**
     * @return void
     * @throws InvalidInputException when something goes wrong during xml file reading
     */
    private function loadFile()
    {
        if (!$this->fileLoaded) {
            $life = $this->loadXmlFile();
            $this->validateXmlFile($life);
            $this->iterationsCount = (int) $life->world->iterations;
            if ($this->iterationsCount < 0) {
                throw new InvalidInputException("Value of element 'iterations' must be zero or positive number");
            }
            $worldSize = (int) $life->world->cells;
            if ($worldSize <= 0) {
                throw new InvalidInputException("Value of element 'cells' must be positive number");
            }
            $speciesCount = (int) $life->world->species;
            if ($speciesCount <= 0) {
                throw new InvalidInputException("Value of element 'species' must be positive number");
            }
            $cells = $this->readCells($life, $worldSize, $speciesCount);
            $this->initialWorld = new World($worldSize, $speciesCount, $cells);
            $this->fileLoaded = true;
        }
    }

    /**
     * @return \SimpleXMLElement
     * @throws InvalidInputException when something goes wrong during xml file reading
     */
    private function loadXmlFile()
    {
        if (!file_exists($this->filePath)) {
            throw new InvalidInputException("Unable to read nonexistent file");
        }
        try {
            libxml_use_internal_errors(true);
            $life = simplexml_load_file($this->filePath);
            $errors = libxml_get_errors();
            libxml_clear_errors();
            if (count($errors) > 0) {
                throw new InvalidInputException("Cannot read XML file");
            }
        }
        catch (\Exception $e) {
            throw new InvalidInputException("Cannot read XML file");
        }
        return $life;
    }

    /**
     * @param \SimpleXMLElement $life
     * @return void
     * @throws InvalidInputException when something goes wrong during xml file reading
     */
    private function validateXmlFile(\SimpleXMLElement $life)
    {
        if (!isset($life->world)) {
            throw new InvalidInputException("Missing element 'world'");
        }
        if (!isset($life->world->iterations)) {
            throw new InvalidInputException("Missing element 'iterations'");
        }
        if (!isset($life->world->cells)) {
            throw new InvalidInputException("Missing element 'cells'");
        }
        if (!isset($life->world->species)) {
            throw new InvalidInputException("Missing element 'species'");
        }
        if (!isset($life->organisms)) {
            throw new InvalidInputException("Missing element 'organisms'");
        }
        foreach ($life->organisms->organism as $organism) {
            if (!isset($organism->x_pos)) {
                throw new InvalidInputException("Missing element 'x_pos' in some of the element 'organism'");
            }
            if (!isset($organism->y_pos)) {
                throw new InvalidInputException("Missing element 'y_pos' in some of the element 'organism'");
            }
            if (!isset($organism->species)) {
                throw new InvalidInputException("Missing element 'species' in some of the element 'organism'");
            }
        }
    }

    /**
     * @param \SimpleXMLElement $life
     * @param int $worldSize
     * @param int $speciesCount
     * @return Cell[][]
     * @throws InvalidInputException when something goes wrong during xml file reading
     */
    private function readCells(\SimpleXMLElement $life, int $worldSize, int $speciesCount): array
    {
        $cells = [];
        foreach ($life->organisms->organism as $organism) {
            $x = (int) $organism->x_pos;
            if ($x < 0 || $x >= $worldSize) {
                throw new InvalidInputException("Value of element 'x_pos' of element 'organism' must be between 0 and number of cells");
            }
            $y = (int) $organism->y_pos;
            if ($y < 0 || $y >= $worldSize) {
                throw new InvalidInputException("Value of element 'y_pos' of element 'organism' must be between 0 and number of cells");
            }
            $species = (int) $organism->species;
            if ($species < 0 || $species >= $speciesCount) {
                throw new InvalidInputException("Value of element 'species' of element 'organism' must be between 0 and maximal number of species");
            }
            $cells[$y] = $cells[$y] ?? [];
            $cells[$y][$x] = new Cell($species);
        }
        for ($y = 0; $y < $worldSize; $y++) {
            $cells[$y] = $cells[$y] ?? [];
            for ($x = 0; $x < $worldSize; $x++) {
                if (!isset($cells[$y][$x])) {
                    $cells[$y][$x] = new Cell();
                }
            }
        }
        return $cells;
    }

}
