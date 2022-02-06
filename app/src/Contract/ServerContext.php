<?php

declare(strict_types=1);

namespace TwitterWebbot\Contract;

use Symfony\Component\Console\Output\OutputInterface;

final class ServerContext
{
    private OutputInterface $output;
    private ConfigurationInterface $configuration;
    private ResponseTextBuilderInterface $responseTextBuilder;

    public function __construct(
        OutputInterface              $output,
        ConfigurationInterface       $configuration,
        ResponseTextBuilderInterface $responseTextBuilder
    )
    {
        $this->output = $output;
        $this->configuration = $configuration;
        $this->responseTextBuilder = $responseTextBuilder;
    }

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    public function getConfiguration(): ConfigurationInterface
    {
        return $this->configuration;
    }

    public function getResponseTextBuilder(): ResponseTextBuilderInterface
    {
        return $this->responseTextBuilder;
    }
}
