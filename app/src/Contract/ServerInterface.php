<?php

declare(strict_types=1);

namespace TwitterWebbot\Contract;

interface ServerInterface
{
    public function __invoke(ServerContext $context): void;
}
