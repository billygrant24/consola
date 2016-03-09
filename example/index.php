<?php

require getcwd() . '/vendor/autoload.php';

use Consola\Application;
use Consola\Command\Command;
use League\Plates\Engine;

$app = new Application();
$app->setTemplate(new Engine(__DIR__ . '/views'));

$app->addCommand('greet',
    /**
     * Send a greeting to the person specified.
     *
     * @arg person    : The name of the person you wish to greet.
     * @opt --s|shout : Be extra enthusiastic and use all caps
     */
    function ($args, $opts) {
        $this->render('greeting', compact('args', 'opts'));
    }
);

$app->addCommand('goodbye',
    /**
     * Bid goodbye to the person specified.
     * @arg person : The name of the person you wish bid goodbye
     */
    function ($args, $opts) {
        $this->info(
            sprintf('Goodbye, %s!', $args['person'])
        );
    }
);

$app->run();
