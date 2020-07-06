<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Process\Process;

require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(dirname(__DIR__) . '/config/bootstrap.php')) {
    require dirname(__DIR__) . '/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

/** @var Process[] $processes */
$processes = [
    // Delete the "tests" database.
    new Process(['php', 'bin/console', 'doctrine:database:drop', '-n', '--force', '--if-exists']),
    // Create the "tests" database.
    new Process(['php', 'bin/console', 'doctrine:database:create', '-n', '--if-not-exists']),
    // Initialize the "tests" database structure.
    new Process(['php', 'bin/console', 'doctrine:migrations:migrate', '-n']),
    // Clear the cache.
    new Process(['php', 'bin/console', 'cache:clear', '--no-warmup']),
];

foreach ($processes as $process) {
    $process->run();
    if (! $process->isSuccessful()) {
        throw new RuntimeException(
            $process->getCommandLine() .
            ': ' .
            $process->getExitCode() .
            ' ' .
            $process->getExitCodeText() .
            ' ' .
            $process->getErrorOutput()
        );
    }
}
