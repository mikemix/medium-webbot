<?php

use Pimple\Container;
use React\Http;
use TwitterWebbot\Contract;
use TwitterWebbot\Implementation;
use TwitterWebbot\Implementation\ReactServer\OAuth1AuthorizationStringBuilder;

require __DIR__ . '/vendor/autoload.php';

$container = new Container();

$container[Contract\ConfigurationInterface::class] = static function (): Contract\ConfigurationInterface {
    return new Implementation\Configuration\IniFileConfiguration(__DIR__ . '/keys.ini');
};

$container[Contract\JwstTelemetryProviderInterface::class] = static function (): Contract\JwstTelemetryProviderInterface {
    return new Implementation\JamesWebbApi\JwstTelemetryProviderCurl();
};

$container[Contract\ResponseTextBuilderInterface::class] = static function () use ($container): Contract\ResponseTextBuilderInterface {
    /** @var Contract\ConfigurationInterface $configuration */
    $configuration = $container[Contract\ConfigurationInterface::class];

    /** @var Contract\JwstTelemetryProviderInterface $telemetryProvider */
    $telemetryProvider = $container[Contract\JwstTelemetryProviderInterface::class];

    return new Implementation\ResponseText\ResponseTextBuilder($telemetryProvider, $configuration);
};

$container[Contract\ServerInterface::class] = static function () use ($container): Contract\ServerInterface {
    $baseUrl = 'https://api.twitter.com/2/';
    $browser = (new Http\Browser())->withBase($baseUrl);

    /** @var Contract\ConfigurationInterface $configuration */
    $configuration = $container[Contract\ConfigurationInterface::class];

    return new Implementation\ReactServer\ServerOrchestrator(
        new Implementation\ReactServer\TweetReceiveAction($browser),
        new Implementation\ReactServer\TweetRespondAction(
            $browser,
            new OAuth1AuthorizationStringBuilder($configuration, $baseUrl),
        ),
    );
};

return $container;
