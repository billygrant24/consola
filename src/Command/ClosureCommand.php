<?php

namespace Consola\Command;

class ClosureCommand extends CommandAbstract
{
    public function __construct($callback)
    {
        $this->handler = $callback;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $handler = $this->handler;

        return $handler($this);
    }
}
