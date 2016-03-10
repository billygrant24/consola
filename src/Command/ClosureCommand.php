<?php

namespace Consola\Command;

class ClosureCommand extends CommandAbstract
{
    public function __construct($callback)
    {
        $this->handler = $callback;
    }

    public function handle()
    {
    }
}
