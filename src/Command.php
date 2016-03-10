<?php

namespace Consola;

use Closure;
use Illuminate\Console\Command as ConsoleCommand;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionFunction;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends ConsoleCommand implements CommandInterface
{
    protected $handler = null;

    public function __construct($name = null, $command = null)
    {
        if ($command instanceof Closure) {
            $this->constructFromClosure($name, $command);
        }

        parent::__construct();
    }

    public function constructFromClosure($name, Closure $command)
    {
        $this->handler = $command;
        $this->signature = $name;

        $fn = new ReflectionFunction($command);

        if ($fn->getDocComment()) {
            $factory = DocBlockFactory::createInstance();
            $doc = $factory->create($fn->getDocComment());

            $signature = array_map(function ($v) {
                return '{' . trim($v) . '}';
            }, array_merge(
                $doc->getTagsByName('arg'),
                $doc->getTagsByName('opt')
            ));

            array_unshift($signature, $name);

            $this->signature = implode(' ', $signature);
            $this->description = trim($doc->getSummary());
        }
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
        if ($this->handler instanceof Closure) {
            $handler = Closure::bind($this->handler, $this);

            return $handler(
                collect($this->argument()),
                collect($this->option())
            );
        }

        return $this->handle(
            collect($this->argument()),
            collect($this->option())
        );
    }

    public function handle()
    {
    }
}
