<?php

namespace Consola\Command;

use Closure;
use Consola\Command;
use Illuminate\Console\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class CommandAbstract extends ConsoleCommand implements Command
{
    public function __construct()
    {
    }

    /**
     * Create a new console command instance.
     *
     * @return void
     */
    public function setUp()
    {
        // We will go ahead and set the name, description, and parameters on console
        // commands just to make things a little easier on the developer. This is
        // so they don't have to all be manually specified in the constructors.
        if (isset($this->signature)) {
            $this->configureUsingFluentDefinition();
        } else {
            parent::__construct($this->name);
        }

        parent::setDescription($this->description);

        if (! isset($this->signature)) {
            $this->specifyParameters();
        }
    }

    public function setDescription($description)
    {
        $this->description = $this->description ? $this->description : $description;
    }

    public function setSignature($signature)
    {
        $this->signature = $this->signature ? $this->signature : $signature;
    }

    protected function hasHandler()
    {
        return $this->handler instanceof Closure;
    }

    /**
     * Execute the console command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->hasHandler()) {
            $handler = Closure::bind($this->handler, $this);

            return $handler(
                collect($this->argument()),
                collect($this->option())
            );
        }

        return $this->handle();
    }
}
