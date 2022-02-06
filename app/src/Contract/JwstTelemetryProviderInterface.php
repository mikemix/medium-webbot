<?php

declare(strict_types=1);

namespace TwitterWebbot\Contract;

interface JwstTelemetryProviderInterface
{
    /**
     * @return JwstTelemetry
     *
     * @throws \RuntimeException If failed to fetch
     */
    public function __invoke(): JwstTelemetry;
}
