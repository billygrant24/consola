<?php

namespace Consola\Command;

use Illuminate\Console\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class CommandAbstract extends ConsoleCommand implements Command
{
    public function __construct()
    {
        //
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function render($name, $data = [])
    {
        $template = $this->template->render($name, array_merge([
            'cmd' => $this,
        ], $data));

        return $this->line(trim($template));
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
        $this->description = $this->description ?? $description;
    }

    public function setSignature($signature)
    {
        $this->signature = $this->signature ?? $signature;
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
        return $this->handle();
    }
}
