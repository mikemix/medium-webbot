<?php

declare(strict_types=1);

namespace TwitterWebbot\Implementation\ReactServer;

use Fig\Http\Message\RequestMethodInterface;
use React\Http\Browser;
use TwitterWebbot\Contract\ResponseText;
use TwitterWebbot\Contract\ServerContext;
use TwitterWebbot\Contract\TweetInterface;

class TweetRespondAction
{
    private const TWEETS_URI = 'tweets';

    private Browser $browser;
    private OAuth1AuthorizationStringBuilder $authorizationStringBuilder;

    public function __construct(Browser $browser, OAuth1AuthorizationStringBuilder $authorizationStringBuilder)
    {
        $this->browser = $browser;
        $this->authorizationStringBuilder = $authorizationStringBuilder;
    }

    public function __invoke(ServerContext $context, TweetInterface $respondTo, ResponseText $response): void
    {
        $output = $context->getOutput();

        if (empty($response->text())) {
            $output->writeln(sprintf('[%s] ignored:   %s', \date('Y-m-d H:i:s'), $respondTo->text()));
            return;
        }

        $output->writeln(sprintf('[%s] received:  %s', \date('Y-m-d H:i:s'), $respondTo->text()));

        $authorization = ($this->authorizationStringBuilder)(RequestMethodInterface::METHOD_POST, self::TWEETS_URI);

        $promise = $this->browser->post(
            self::TWEETS_URI,
            [
                'Accept' => 'application/json',
                'Authorization' => $authorization,
                'Content-type' => 'application/json',
            ],
            \json_encode([
                'reply' => ['in_reply_to_tweet_id' => $respondTo->id()],
                'text' => $response->text(),
            ]),
        );

        $promise->then(
            static function () use ($output, $response): void {
                $output->writeln(sprintf('[%s] responded: %s', \date('Y-m-d H:i:s'), $response->text()));
            },
            static function (\Exception $exception) use ($output): void {
                $output->writeln(sprintf('[%s] response error: %s', \date('Y-m-d H:i:s'), $exception->getMessage()));
            }
        );
    }
}
