<?php

declare(strict_types=1);

namespace TwitterWebbot\Implementation\ReactServer;

use TwitterWebbot\Contract\ConfigurationInterface;

final class OAuth1AuthorizationStringBuilder
{
    private ConfigurationInterface $configuration;
    private string $baseUrl;

    public function __construct(ConfigurationInterface $configuration, string $baseUrl)
    {
        $this->configuration = $configuration;
        $this->baseUrl = $baseUrl;
    }

    public function __invoke(string $requestMethod, string $requestUri): string
    {
        /** @var array<string, string> $params */
        $params = [
            'oauth_consumer_key' => $this->configuration->apiKey(),
            'oauth_nonce' => \bin2hex(\random_bytes(8)),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => \time(),
            'oauth_token' => $this->configuration->oauthToken(),
            'oauth_version' => '1.0',
        ];

        $params = \array_combine(
            \array_map('urlencode', \array_keys($params)),
            \array_map('urlencode', \array_values($params)),
        );

        \ksort($params);

        $signatureString = \sprintf(
            '%s&%s&%s',
            \strtoupper($requestMethod),
            \urlencode($this->baseUrl . $requestUri),
            \urlencode(\http_build_query($params)),
        );

        $signingKey = \sprintf('%s&%s', $this->configuration->apiSecret(), $this->configuration->oauthSecret());
        $params['oauth_signature'] = \urlencode(\base64_encode(\hash_hmac('sha1', $signatureString, $signingKey, true)));
        \ksort($params);

        $authorizationParts = [];
        foreach ($params as $key => $value) {
            $authorizationParts[] = \sprintf('%s="%s"', $key, $value);
        }

        return 'OAuth ' . \implode(', ', $authorizationParts);
    }
}
