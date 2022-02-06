<?php

use Symfony\Component\Console;
use TwitterWebbot\Contract;
use TwitterWebbot\Contract\ServerContext;

$container = require __DIR__ . '/bootstrap.php';

(new Console\SingleCommandApplication())
    ->setName('Twitter JWST bot')
    ->setVersion('0.1')
    ->setCode(static function () use ($container): int {
        /** @var Console\Output\OutputInterface $output */
        [, $output] = \func_get_args();

        try {
            /** @var Contract\ConfigurationInterface $configuration */
            $configuration = $container[Contract\ConfigurationInterface::class];
        } catch (\Throwable $throwable) {
            $output->writeln(sprintf('<error>Configuration invalid</error>: %s', $throwable->getMessage()));

            return Console\Command\Command::INVALID;
        }

        /** @var Contract\ResponseTextBuilderInterface $response */
        $response = $container[Contract\ResponseTextBuilderInterface::class];

        /** @var Contract\ServerInterface $server */
        $server = $container[Contract\ServerInterface::class];

        ($server)(new ServerContext($output, $configuration, $response));

        return Console\Command\Command::SUCCESS;
    })
    ->run();
