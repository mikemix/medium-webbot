<?php

declare(strict_types=1);

namespace TwitterWebbot\Implementation\JamesWebbApi;

use TwitterWebbot\Contract\JwstTelemetry;
use TwitterWebbot\Contract\JwstTelemetryProviderInterface;

final class JwstTelemetryProviderMock implements JwstTelemetryProviderInterface
{
    public function __invoke(): JwstTelemetry
    {
        return new JwstTelemetry(
            new \DateTimeImmutable(),
            'WEBB IS ORBITING L2 - Next Steps:  Cooldown, Alignment, Calibration',
            \random_int(20, 40),
            \random_int(-230, -200),
            \random_int(-190, -170),
        );
    }
}
