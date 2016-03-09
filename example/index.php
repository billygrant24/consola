<?php

require getcwd() . '/vendor/autoload.php';

use Consola\Application;
use Consola\Command\Command;

$app = new Application();

$app->bind(
    'greet
        { person : The name of the person you wish to greet }
        { --s|shout : Be extra enthusiastic and use all caps }',
    'Send a greeting to the person specified',
    function (Command $cmd) {
        $person = $cmd->argument('person');

        $cmd->info(sprintf(
            'Good evening, %s!',
            $cmd->option('shout') ? strtoupper($person) : $person
        ));
    }
);

$app->bind(
    'goodbye
        { person : The name of the person you wish bid goodbye }',
    'Bid goodbye to the person specified',
    function (Command $cmd) {
        $cmd->info(sprintf('Goodbye, %s!', $cmd->argument('person')));
    }
);

$app->run();
