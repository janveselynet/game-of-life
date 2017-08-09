<?php

namespace Life\IO;

use Life\Environment\World;
use Life\Exceptions\OutputWritingException;

/**
 * Basic interface for classes writing game output
 */
interface IOutputWriter
{

    /**
     * @param World $world state of world that should be saved
     * @return void
     * @throws OutputWritingException when writing output failed for some reason
     */
    public function saveWorld(World $world);

}
