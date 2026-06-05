<?php

namespace Phunk;

class NotFound
{
    public function __construct(protected ?string $message = null)
    {}

    public function getMessage(): ?string
    {
        return $this->message;
    }
}