<?php

declare(strict_types=1);

namespace TwitterWebbot\Contract;

interface TweetInterface
{
    public function id(): string;

    public function text(): string;
}
