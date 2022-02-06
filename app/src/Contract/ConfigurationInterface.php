<?php

declare(strict_types=1);

namespace TwitterWebbot\Contract;

interface ConfigurationInterface
{
    public function apiKey(): string;

    public function apiSecret(): string;

    public function apiBearer(): string;

    public function oauthToken(): string;

    public function oauthSecret(): string;

    public function botHandle(): string;
}
