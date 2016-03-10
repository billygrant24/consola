<?php

require __DIR__ . '/../vendor/autoload.php';

use Consola\Application;

$app = new Application();

/**
 * Send a greeting to the person specified.
 *
 * @arg person    : The name of the person you wish to greet.
 * @opt --s|shout : Be extra enthusiastic and use all caps
 */
$app->addCommand('greet', function ($args, $opts) {
    $person = $opts['shout'] ? strtoupper($args['person']) : $args['person'];

    return $this->info(sprintf('Good evening, %s', $person));
});

/**
 * Bid goodbye to the person specified.
 * @arg person : The name of the person you wish bid goodbye
 */
$app->addCommand('goodbye', function ($args, $opts) {
    $this->info(
        sprintf('Goodbye, %s!', $args['person'])
    );
});

$app->run();
