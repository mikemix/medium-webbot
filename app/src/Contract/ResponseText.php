<?php

declare(strict_types=1);

namespace TwitterWebbot\Contract;

final class ResponseText
{
    private string $response;

    private function __construct(string $response)
    {
        $this->response = $response;
    }

    public static function empty(): self
    {
        return new self('');
    }

    public static function answer(string $answer): self
    {
        return new self($answer);
    }

    public function text(): string
    {
        return $this->response;
    }
}
