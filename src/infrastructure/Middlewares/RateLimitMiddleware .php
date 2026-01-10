<?php

declare(strict_types=1);

namespace Infrastructure\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Nyholm\Psr7\Response;

class RateLimitMiddleware implements MiddlewareInterface
{
    private RedisClient $redis;
    private int $limit;
    private int $window;

    public function __construct()
    {
        $redis = new RedisClient([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]);
        $this->limit = 100;
        $this->window = 60;

        // so 100 req/min per IP
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $ip = $request->getServerParams()['remote_addr'] ?? 'unknown';

        $now = time();
        $windowStart = intdiv($now, $this->window) * $this->window;
        $key = "ratelimit:{$ip}:{$windowStart}";

        // Atomic increment
        $count = $this->redis->incr($key);

        if ($count === 1) {
            // first request in this window → set expiry
            $this->redis->expire($key, $this->window);
        }

        if ($count > $this->limit) {
            return new Response(
                429,
                [
                    'Content-Type' => 'application/json',
                    'Retry-After'  => (string)$this->window
                ],
                json_encode(['error' => 'Too Many Requests'])
            );
        }

        return $handler->handle($request);
    }
}
