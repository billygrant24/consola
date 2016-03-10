# Consola

Consola is an exquisitely simple PHP framework for building small console
applications. It is inspired by micro-frameworks for the web such as Lumen,
Slim and Silex.

## Usage

```
<?php // cli.php

require __DIR__ . '/vendor/autoload.php';

$cli = new Consola\Console();

/**
 * Send a greeting to the person specified
 *
 * @arg person   : The name of the person you wish to greet
 * @opt --y|yell : Be extra enthusiastic and use all caps
 */
$cli->addCommand('greet', function ($args, $opts) {
    $greeting = sprintf(
        'Hello, %s',
        $opts['yell'] ? strtoupper($args['person']) . '!' : $args['person']
    );

    $this->line($greeting);
});

$cli->run();
```

Running the `greet` command in your terminal should yield the following result.

```
$ php cli.php greet world --yell
Hello, WORLD!
```

## License

Consola is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
