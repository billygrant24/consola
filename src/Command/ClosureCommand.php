<?php

namespace Consola\Command;

use Closure;

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
        $handler = Closure::bind($this->handler, $this);

        return $handler(
            collect($this->argument()),
            collect($this->option())
        );
    }
}
