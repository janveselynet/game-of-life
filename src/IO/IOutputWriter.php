<?php

namespace Life\IO;

use Life\Environment\World;

interface IOutputWriter
{

    /**
     * @param World $world state of world that should be saved
     * @return void
     */
    public function saveWorld(World $world);

}
