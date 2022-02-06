<?php

declare(strict_types=1);

namespace TwitterWebbot\Implementation\ReactServer;

use TwitterWebbot\Contract\TweetInterface;

final class Tweet implements TweetInterface
{
    private string $tweetId;
    private string $text;

    private function __construct(string $tweetId, string $text)
    {
        assert(!empty($tweetId));
        assert(!empty($text));
        $this->tweetId = $tweetId;
        $this->text = $text;
    }

    public static function fromJson(string $jsonString): self
    {
        /** @var array{data: array{id: non-empty-string, text: non-empty-string}} $data */
        $data = \json_decode($jsonString, true, 10, JSON_THROW_ON_ERROR);

        ['data' => ['id' => $tweetId, 'text' => $text]] = $data;

        return new self($tweetId, $text);
    }

    public function id(): string
    {
        return $this->tweetId;
    }

    public function text(): string
    {
        return $this->text;
    }
}
