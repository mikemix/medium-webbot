<?php

declare(strict_types=1);

namespace TwitterWebbot\Contract;

final class JwstTelemetry
{
    private \DateTimeInterface $lastUpdate;
    private string $deploymentStep;
    private int $temperatureHotSideAverage;
    private int $temperatureColdSideAverage;
    private int $temperatureSensorsAverage;

    public function __construct(
        \DateTimeInterface $lastUpdate,
        string             $deploymentStep,
        int                $temperatureHotSideAverage,
        int                $temperatureColdSideAverage,
        int                $temperatureSensorsAverage
    )
    {
        $this->lastUpdate = $lastUpdate;
        $this->deploymentStep = $deploymentStep;
        $this->temperatureHotSideAverage = $temperatureHotSideAverage;
        $this->temperatureColdSideAverage = $temperatureColdSideAverage;
        $this->temperatureSensorsAverage = $temperatureSensorsAverage;
    }

    public function lastUpdate(): \DateTimeInterface
    {
        return $this->lastUpdate;
    }

    public function deploymentStep(): string
    {
        return $this->deploymentStep;
    }

    public function temperatureHotSideAverage(): int
    {
        return $this->temperatureHotSideAverage;
    }

    public function temperatureColdSideAverage(): int
    {
        return $this->temperatureColdSideAverage;
    }

    public function temperatureSensorsAverage(): int
    {
        return $this->temperatureSensorsAverage;
    }
}
