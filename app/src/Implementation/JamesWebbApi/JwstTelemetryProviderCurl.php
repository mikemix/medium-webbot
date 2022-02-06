<?php

declare(strict_types=1);

namespace TwitterWebbot\Implementation\JamesWebbApi;

use TwitterWebbot\Contract\JwstTelemetry;
use TwitterWebbot\Contract\JwstTelemetryProviderInterface;

final class JwstTelemetryProviderCurl implements JwstTelemetryProviderInterface
{
    private const API_URL = 'https://api.jwst-hub.com/track';

    private string $apiUrl;

    public function __construct(string $apiUrl = self::API_URL)
    {
        $this->apiUrl = $apiUrl;
    }

    /** {@inheritDoc} */
    public function __invoke(): JwstTelemetry
    {
        $ch = \curl_init($this->apiUrl);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = \curl_exec($ch);

        if (!is_string($response) || empty($response)) {
            throw new \RuntimeException('JWST API response failed to fetch');
        }

        try {
            /** @var array{
             *     currentDeploymentStep: string,
             *     timestamp: string,
             *     tempC: array{
             *         tempWarmSide1C: int,
             *         tempWarmSide2C: int,
             *         tempCoolSide1C: int,
             *         tempCoolSide2C: int,
             *         tempInstMiriC: int,
             *         tempInstNirCamC: int,
             *         tempInstNirSpecC: int,
             *         tempInstFgsNirissC: int,
             *         tempInstFsmC: int,
             *     },
             * } $result
             */
            $result = \json_decode($response, true, 10, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new \RuntimeException('JWST API invalid JSON response');
        }

        return new JwstTelemetry(
            new \DateTimeImmutable($result['timestamp']),
            $result['currentDeploymentStep'],
            $this->averageTemperature(
                $result['tempC']['tempWarmSide1C'],
                $result['tempC']['tempWarmSide2C'],
            ),
            $this->averageTemperature(
                $result['tempC']['tempCoolSide1C'],
                $result['tempC']['tempCoolSide2C'],
            ),
            $this->averageTemperature(
                $result['tempC']['tempInstMiriC'],
                $result['tempC']['tempInstNirCamC'],
                $result['tempC']['tempInstNirSpecC'],
                $result['tempC']['tempInstFgsNirissC'],
                $result['tempC']['tempInstFsmC'],
            ),
        );
    }

    private function averageTemperature(int ...$temperatures): int
    {
        return (int)\round(\array_sum($temperatures) / \count($temperatures), 0);
    }
}
