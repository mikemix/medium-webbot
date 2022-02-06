<?php

declare(strict_types=1);

namespace TwitterWebbot\Contract;

interface ResponseTextBuilderInterface
{
    public function __invoke(TweetInterface $tweet): ResponseText;
}
