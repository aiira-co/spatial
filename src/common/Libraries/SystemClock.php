<?php
declare(strict_types=1);
namespace Common\Libraries;

use Psr\Clock\ClockInterface;

final class SystemClock implements ClockInterface
{
    private \DateTimeImmutable $now;

    public function __construct()
    {
        $this->now = $now ?? new \DateTimeImmutable();
    }

    public function now(): \DateTimeImmutable
    {
        return $this->now;
    }
}
