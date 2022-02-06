<?php

namespace TwitterWebbot\Contract;

interface TweetInterface
{
    public function id(): string;

    public function text(): string;
}
