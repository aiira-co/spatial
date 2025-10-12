<?php
declare(strict_types=1);

namespace Infrastructure\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Nyholm\Psr7\Response;
use Npgsql\Connection; // If using ext-pgsql, change accordingly

class BasicAuthMiddleware implements MiddlewareInterface
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Basic ')) {
            return $this->unauthorized();
        }

        $encoded = substr($authHeader, 6); // strip "Basic "
        $decoded = base64_decode($encoded, true);
        if (!$decoded || !str_contains($decoded, ':')) {
            return $this->unauthorized();
        }

        [$username, $password] = explode(':', $decoded, 2);

        // Get client IP
        $ip = $request->getServerParams()['remote_addr'] ?? null;

        // Validate against Postgres
        $stmt = $this->pdo->prepare('SELECT auth."ValidateBasicAuth"(:u, :p, :ip)');
        $stmt->execute([
            ':u' => $username,
            ':p' => $password,
            ':ip' => $ip,
        ]);

        $result = $stmt->fetchColumn();
        if (!$result) {
            return $this->unauthorized();
        }

        $json = json_decode($result, true);
        if (!isset($json['Valid']) || !$json['Valid']) {
            return $this->unauthorized();
        }

        $channelId = $json['ChannelId'];

        // Attach channel ID to request attributes
        $request = $request->withAttribute('ChannelId', $channelId);

        return $handler->handle($request);
    }

    private function unauthorized(): ResponseInterface
    {
        return new Response(
            401,
            ['WWW-Authenticate' => 'Basic realm="API"'],
            'Unauthorized'
        );
    }
}


// usage
// $channelId = $request->getAttribute('ChannelId');
 // Use $channelId to filter access / log activity

