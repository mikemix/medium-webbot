<?php

declare(strict_types=1);

namespace TwitterWebbot\Implementation\Configuration;

use TwitterWebbot\Contract\ConfigurationInterface;

final class IniFileConfiguration implements ConfigurationInterface
{
    private ArrayConfiguration $configuration;

    public function __construct(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException(\sprintf('Configuration file "%s" missing', $filePath));
        }

        $values = \parse_ini_file($filePath);

        if (!\is_array($values)) {
            throw new \RuntimeException(\sprintf('Configuration file "%s" invalid format', $filePath));
        }

        $this->configuration = new ArrayConfiguration($values);
    }

    public function apiKey(): string
    {
        return $this->configuration->apiKey();
    }

    public function apiSecret(): string
    {
        return $this->configuration->apiSecret();
    }

    public function apiBearer(): string
    {
        return $this->configuration->apiBearer();
    }

    public function oauthToken(): string
    {
        return $this->configuration->oauthToken();
    }

    public function oauthSecret(): string
    {
        return $this->configuration->oauthSecret();
    }

    public function botHandle(): string
    {
        return $this->configuration->botHandle();
    }
}
