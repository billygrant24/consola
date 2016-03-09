<?php

namespace Consola;

use Closure;
use Consola\Command\Command;
use Consola\Command\ClosureCommand;
use Consola\Exception\IllegalCommandException;
use Exception;
use Illuminate\Contracts\Console\Application as ApplicationContract;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class Application extends SymfonyApplication implements ApplicationContract
{
    const VERSION = 'v0.0.1';

    /**
     * The output from the previous command.
     *
     * @var \Symfony\Component\Console\Output\BufferedOutput
     */
    protected $lastOutput;

    /**
     * @param  string  $version
     * @return void
     */
    public function __construct()
    {
        parent::__construct('Consola', self::VERSION);

        $this->setAutoExit(true);
        $this->setCatchExceptions(true);
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function addCommand($name, $command = null)
    {
        if ($name instanceof SymfonyCommand) {
            return $this->addToParent($command);
        }

        if (is_string($command)) {
            $command = new $command();
        }

        if ($command instanceof Closure) {
            $fn = new \ReflectionFunction($command);
            $doc = new \phpDocumentor\Reflection\DocBlock($fn->getDocComment());

            $modifiers = array_map(function ($m) {
                return '{' . trim($m->getContent()) . '}';
            }, array_merge($doc->getTagsByName('arg'), $doc->getTagsByName('opt')));

            $signature = [];
            $signature['name'] = $name;
            $signature['modifiers'] = $modifiers ? implode(' ', $modifiers) : '';

            $command = new ClosureCommand($command);
            $command->setDescription(trim($doc->getShortDescription()));
            $command->setSignature(implode(' ', $signature));
        }

        // At this stage we should have an instance of Command.

        if ( ! $command instanceof Command) {
            throw new IllegalCommandException(
                sprintf('Commands should implement %s', Command::class)
            );
        }

        $command->setTemplate($this->template);
        $command->setUp();

        return $this->add($command);
    }

    public function displayError(Exception $e)
    {
        return parent::renderException($e, new ConsoleOutput());
    }

    /**
     * Run an Artisan console command by name.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return int
     */
    public function call($command, array $parameters = [])
    {
        $parameters = collect($parameters)->prepend($command);

        $this->lastOutput = new BufferedOutput;

        $this->setCatchExceptions(false);

        $result = $this->run(new ArrayInput($parameters->toArray()), $this->lastOutput);

        $this->setCatchExceptions(true);

        return $result;
    }

    /**
     * Get the output for the last run command.
     *
     * @return string
     */
    public function output()
    {
        return $this->lastOutput ? $this->lastOutput->fetch() : '';
    }

    /**
     * Add the command to the parent instance.
     *
     * @param  \Symfony\Component\Console\Command\Command  $command
     * @return \Symfony\Component\Console\Command\Command
     */
    protected function addToParent(SymfonyCommand $command)
    {
        return parent::add($command);
    }

    /**
     * Get the default input definitions for the applications.
     *
     * This is used to add the --env option to every available command.
     *
     * @return \Symfony\Component\Console\Input\InputDefinition
     */
    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();

        $definition->addOption($this->getEnvironmentOption());

        return $definition;
    }

    /**
     * Get the global environment option for the definition.
     *
     * @return \Symfony\Component\Console\Input\InputOption
     */
    protected function getEnvironmentOption()
    {
        $message = 'The environment the command should run under.';

        return new InputOption('--env', null, InputOption::VALUE_OPTIONAL, $message);
    }
}
