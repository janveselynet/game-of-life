<?php declare(strict_types = 1);

namespace Life\IO;

use Life\Environment\World;

interface IOutputWriter
{
    /**
     * @throws OutputWritingException
     */
    public function saveWorld(World $world): void;
}
