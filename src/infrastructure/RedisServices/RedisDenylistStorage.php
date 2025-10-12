<?php

declare(strict_types=1);

namespace Infrastructure\RedisServices;

interface DenylistStorage
{
    public function add(string $jti, int $ttl): void;
    public function exists(string $jti): bool;
}

class RedisDenylistStorage implements DenylistStorage
{
    public function __construct(private \Redis $redis)
    {
        $this->redis->connect(
            getenv('REDIS_HOST') ?? '127.0.0.1',
            (int) (getenv('REDIS_PORT') ?? 6379)
        );

        if (!empty(getenv('REDIS_PASSWORD'))) {
            $this->redis->auth(getenv('REDIS_PASSWORD'));
        }
    }

    public function add(string $jti, int $ttl): void
    {
        $this->redis->setex("jwt:denylist:$jti", $ttl, 1);
    }

    public function exists(string $jti): bool
    {
        return (bool)$this->redis->exists("jwt:denylist:$jti");
    }
}
