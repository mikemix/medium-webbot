<?php

declare(strict_types=1);

namespace TwitterWebbot\Implementation\ReactServer;

use TwitterWebbot\Contract\ServerContext;
use TwitterWebbot\Contract\ServerInterface;
use TwitterWebbot\Contract\TweetInterface;

final class ServerOrchestrator implements ServerInterface
{
    private TweetReceiveAction $tweetReceiveAction;
    private TweetRespondAction $tweetRespondAction;

    public function __construct(TweetReceiveAction $tweetReceiveAction, TweetRespondAction $tweetRespondAction)
    {
        $this->tweetReceiveAction = $tweetReceiveAction;
        $this->tweetRespondAction = $tweetRespondAction;
    }

    public function __invoke(ServerContext $context): void
    {
        $receivedTweetHandler = function (TweetInterface $tweetReceived) use ($context): void {
            $responseBuilder = $context->getResponseTextBuilder();
            $response = ($responseBuilder)($tweetReceived);
            ($this->tweetRespondAction)($context, $tweetReceived, $response);
        };

        ($this->tweetReceiveAction)($context, $receivedTweetHandler);
    }
}
