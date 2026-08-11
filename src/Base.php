<?php

namespace Phunk;

use Psr\Log\LoggerInterface;

abstract class Base
{
    public function __construct(protected LoggerInterface $logger)
    {}

    protected function debug(string $message = '', mixed $context = []): mixed
    {
        $message = static::class . ': ' . $message;
        $this->logger->debug($message, static::buildContext($context));

        return $context;
    }

    protected function info(string $message, mixed $context = []): mixed
    {
        $message = static::class . ': ' . $message;
        $this->logger->info($message, static::buildContext($context));

        return $context;
    }

    protected function error(string $message, mixed $context = []): mixed
    {
        $message = static::class . ': ' . $message;
        $this->logger->error($message, static::buildContext($context));

        return $context;
    }

    private static function buildContext(mixed $data): array
    {
        if (is_array($data)) {
            return $data;
        }

        return ['data' => $data];
    }
}