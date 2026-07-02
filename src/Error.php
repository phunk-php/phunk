<?php

namespace Phunk;

class Error
{
    public function __construct(protected ?string $message = null)
    {}

    public function getMessage(): ?string
    {
        return $this->message;
    }
}