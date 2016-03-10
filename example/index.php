<?php

require __DIR__ . '/../vendor/autoload.php';

$cli = new Consola\Console();

/**
 * Send a greeting to the person specified
 *
 * @arg person    : The name of the person you wish to greet
 * @opt --s|shout : Be extra enthusiastic and use all caps
 */
$cli->addCommand('greet', function ($args, $opts) {
    $person = $args['person'];

    if ($opts['shout']) {
        $person = strtoupper($person);
    }

    return $this->info(sprintf('Good evening, %s', $person));
});

/**
 * Check Git repository status
 */
$cli->addCommand('git:status', function ($args, $opts) {
    if ( ! is_dir(__DIR__ . '/../.git')) {
        $this->error('No Git repository initialised');
        exit(0);
    }

    $this->info(shell_exec('git status'));
});

/**
 * Bid goodbye to the person specified
 * @arg person : The name of the person you wish bid goodbye
 */
$cli->addCommand('goodbye', function ($args, $opts) {
    $this->info(sprintf('Goodbye, %s!', $args['person']));
});

$cli->run();
