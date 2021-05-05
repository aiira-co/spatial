<?php

namespace Core\Application\Interfaces;

interface SmsInterface
{
    public function compose(string $message): self;

    public function to(string ...$recipient): self;

    public function from(string $sender): self;

    public function send(): array;
}
