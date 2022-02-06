<?php

declare(strict_types=1);

namespace TwitterWebbot\Implementation\ResponseText;

use TwitterWebbot\Contract\ConfigurationInterface;
use TwitterWebbot\Contract\JwstTelemetry;
use TwitterWebbot\Contract\JwstTelemetryProviderInterface;
use TwitterWebbot\Contract\ResponseText;
use TwitterWebbot\Contract\ResponseTextBuilderInterface;
use TwitterWebbot\Contract\TweetInterface;

final class ResponseTextBuilder implements ResponseTextBuilderInterface
{
    private const SIMILARITY_THRESHOLD_PERCENT = 75;
    private const SUGGESTION_THRESHOLD_PERCENT = 50;

    private JwstTelemetryProviderInterface $telemetryProvider;
    private ConfigurationInterface $configuration;

    public function __construct(JwstTelemetryProviderInterface $telemetryProvider, ConfigurationInterface $configuration)
    {
        $this->telemetryProvider = $telemetryProvider;
        $this->configuration = $configuration;
    }

    public function __invoke(TweetInterface $tweet): ResponseText
    {
        if ($this->tweetDoesNotStartWithBotName($tweet)) {
            return ResponseText::empty();
        }

        $tweetCommand = $this->tweetTextWithoutBotName($tweet);

        if (empty($tweetCommand)) {
            return ResponseText::empty();
        }

        /** @var array<string, callable(JwstTelemetry):string> $commands */
        $commands = [
            'what is the deployment status' => [$this, 'getDeploymentStatus'],
            'what is the temperature' => [$this, 'getMissionTemperature'],
            'when was the last update' => [$this, 'getMissionUpdateTime'],
        ];

        $suggestedCommand = null;

        foreach ($commands as $command => $callable) {
            \similar_text($command, $tweetCommand, $similarityPercent);

            if ($similarityPercent < self::SIMILARITY_THRESHOLD_PERCENT) {
                if ($similarityPercent > self::SUGGESTION_THRESHOLD_PERCENT) {
                    $suggestedCommand = $command;
                }

                continue;
            }

            $telemetry = ($this->telemetryProvider)();

            return ResponseText::answer($callable($telemetry));
        }

        if (null === $suggestedCommand) {
            return ResponseText::empty();
        }

        return ResponseText::answer(sprintf(
            'Sorry, I don\'t understand this question. Did you mean: "%s"?',
            $suggestedCommand,
        ));
    }

    public function getDeploymentStatus(JwstTelemetry $telemetry): string
    {
        return $telemetry->deploymentStep();
    }

    public function getMissionTemperature(JwstTelemetry $telemetry): string
    {
        return \sprintf(
            'Hot side average %d°C, cold side average %d°C, sensors average %d°C',
            $telemetry->temperatureHotSideAverage(),
            $telemetry->temperatureColdSideAverage(),
            $telemetry->temperatureSensorsAverage(),
        );
    }

    public function getMissionUpdateTime(JwstTelemetry $telemetry): string
    {
        return \sprintf('Last update was on %s', $telemetry->lastUpdate()->format('Y-m-d H:i:s \U\T\C'));
    }

    private function tweetDoesNotStartWithBotName(TweetInterface $tweet): bool
    {
        return 0 !== \strpos($tweet->text(), \sprintf('@%s', $this->configuration->botHandle()));
    }

    private function tweetTextWithoutBotName(TweetInterface $tweet): string
    {
        return \trim(\strtr(
            $tweet->text(),
            [\sprintf('@%s', $this->configuration->botHandle()) => '']
        ));
    }
}
