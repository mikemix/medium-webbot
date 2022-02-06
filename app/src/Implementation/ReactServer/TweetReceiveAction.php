<?php

declare(strict_types=1);

namespace TwitterWebbot\Implementation\ReactServer;

use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use React\Http\Browser;
use React\Stream\ReadableStreamInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TwitterWebbot\Contract\ConfigurationInterface;
use TwitterWebbot\Contract\ServerContext;

class TweetReceiveAction
{
    private const STREAM_RULE_URI = 'tweets/search/stream/rules';
    private const STREAM_URI = 'tweets/search/stream';

    private Browser $browser;

    public function __construct(Browser $browser)
    {
        $this->browser = $browser;
    }

    public function __invoke(ServerContext $context, \Closure $onTweet): void
    {
        $output = $context->getOutput();
        $configuration = $context->getConfiguration();

        $this->setUpStreamRuleToFollowBotTweets($configuration);
        $this->setUpStreamListener($output, $configuration, $onTweet);
    }

    private function setUpStreamRuleToFollowBotTweets(ConfigurationInterface $configuration): void
    {
        $this->browser->post(
            self::STREAM_RULE_URI,
            [
                'Content-Type' => 'application/json',
                'Authorization' => \sprintf('Bearer %s', $configuration->apiBearer()),
            ],
            \json_encode(['add' => [
                ['value' => \sprintf('@%s has:mentions -is:quote -is:retweet', $configuration->botHandle())],
            ]])
        )->then(null, function (\Exception $throwable) {
            throw $throwable;
        });
    }

    private function setUpStreamListener(OutputInterface $output, ConfigurationInterface $configuration, \Closure $onTweet): void
    {
        $this->browser->requestStreaming(
            RequestMethodInterface::METHOD_GET,
            self::STREAM_URI,
            [
                'Content-Type' => 'application/json',
                'Authorization' => \sprintf('Bearer %s', $configuration->apiBearer()),
            ],
        )->then(
            function (ResponseInterface $response) use ($output, $configuration, $onTweet): void {
                $body = $response->getBody();
                assert($body instanceof StreamInterface);
                assert($body instanceof ReadableStreamInterface);

                $output->writeln(\sprintf('Listening to the stream of @%s…', $configuration->botHandle()));

                $body->on('data', function (string $chunk) use ($onTweet): void {
                    try {
                        $onTweet(Tweet::fromJson($chunk));
                    } catch (\Exception $exception) {
                        return;
                    }
                });
            },
            function (\Exception $exception) use ($output): void {
                $output->writeln(\sprintf('<error>Not listening to the stream: %s</error>', $exception->getMessage()));
            }
        );
    }
}
