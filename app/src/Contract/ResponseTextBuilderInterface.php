<?php

namespace TwitterWebbot\Contract;

interface ResponseTextBuilderInterface
{
    public function __invoke(TweetInterface $tweet): ResponseText;
}
