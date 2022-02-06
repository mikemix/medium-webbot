<?php

declare(strict_types=1);

namespace TwitterWebbot\Implementation\Configuration;

use TwitterWebbot\Contract\ConfigurationInterface;

final class ArrayConfiguration implements ConfigurationInterface
{
    private string $apiKey;
    private string $apiSecret;
    private string $apiBearer;
    private string $oauthToken;
    private string $oauthSecret;
    private string $botHandle;

    public function __construct(array $values)
    {
        $this->apiKey = $this->assign($values, 'TWITTER_API_KEY');
        $this->apiSecret = $this->assign($values, 'TWITTER_API_SECRET');
        $this->apiBearer = $this->assign($values, 'TWITTER_BEARER');
        $this->oauthToken = $this->assign($values, 'TWITTER_OAUTH_TOKEN');
        $this->oauthSecret = $this->assign($values, 'TWITTER_OAUTH_SECRET');
        $this->botHandle = $this->assign($values, 'BOT_HANDLE');
    }

    private function assign(array $values, string $key): string
    {
        if (empty($values[$key]) || !is_string($values[$key])) {
            throw new \InvalidArgumentException(sprintf(
                'The configuration is missing the value of the "%s" key',
                $key,
            ));
        }

        return $values[$key];
    }

    public function apiKey(): string
    {
        return $this->apiKey;
    }

    public function apiSecret(): string
    {
        return $this->apiSecret;
    }

    public function apiBearer(): string
    {
        return $this->apiBearer;
    }

    public function oauthToken(): string
    {
        return $this->oauthToken;
    }

    public function oauthSecret(): string
    {
        return $this->oauthSecret;
    }

    public function botHandle(): string
    {
        return $this->botHandle;
    }
}
