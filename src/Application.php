<?php

namespace Consola;

use Closure;
use Consola\Command\ClosureCommand;
use Consola\Exception\IllegalCommandException;
use Exception;
use Illuminate\Contracts\Console\Application as ApplicationContract;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionFunction;
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

        set_exception_handler([$this, 'handleError']);

        $this->setAutoExit(true);
        $this->setCatchExceptions(true);
    }

    public function handleError(Exception $e)
    {
        return parent::renderException($e, new ConsoleOutput());
    }

    public function addCommand($name, $command = null)
    {
        if ($name instanceof SymfonyCommand) {
            return $this->addToParent($command);
        }

        if ($command instanceof Closure) {
            $fn = new ReflectionFunction($command);
            $command = new ClosureCommand($command);

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

                $command->setSignature(implode(' ', $signature));
                $command->setDescription(trim($doc->getSummary()));
            } else {
                $command->setSignature($name);
            }
        }

        if ( ! $command instanceof Command) {
            throw new IllegalCommandException(
                sprintf('Commands should implement %s', Command::class)
            );
        }

        $command->setUp();

        return $this->add($command);
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
