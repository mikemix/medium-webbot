<?php

namespace TwitterWebbot\Contract;

interface ServerInterface
{
    public function __invoke(ServerContext $context): void;
}
