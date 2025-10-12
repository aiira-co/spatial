<?php
declare(strict_types=1);

namespace Infrastructure\Middlewares;

use Nyholm\Psr7\Response;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class IpAccessMiddleware implements MiddlewareInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $ip = $request->getServerParams()['remote_addr'] ?? null;
        if (!$ip) {
            return $this->forbidden('IP address not detected');
        }

        try {
            // 1️⃣ Log the request
            $stmt = $this->pdo->prepare('SELECT security."LogIpRequest"(:ip)');
            $stmt->execute([':ip' => $ip]);

            // 2️⃣ Get IP info
            $stmt = $this->pdo->prepare('SELECT security."GetIpInfo"(:ip)');
            $stmt->execute([':ip' => $ip]);
            $result = $stmt->fetchColumn();

            if (!$result) {
                return $this->forbidden('Unable to fetch IP info');
            }

            $ipInfo = json_decode($result, true);
            $status = $ipInfo['Status'] ?? 1;

            // 3️⃣ Check status
            switch ($status) {
                case 1: // Active → allow
                    break;

                case 2: // Blocked
                    return $this->forbidden('Your IP is blocked');

                case 3: // Inactive
                    return $this->forbidden('Your IP is inactive');

                default:
                    return $this->forbidden('Unknown IP status');
            }

            // 4️⃣ Attach IP info to request attributes for controllers
            $request = $request->withAttribute('IpInfo', $ipInfo);

            return $handler->handle($request);

        } catch (\Throwable $e) {
            return new Response(
                500,
                ['Content-Type' => 'application/json'],
                json_encode(['error' => 'Internal Server Error', 'details' => $e->getMessage()])
            );
        }
    }

    private function forbidden(string $msg): ResponseInterface
    {
        return new Response(
            403,
            ['Content-Type' => 'application/json'],
            json_encode(['error' => $msg])
        );
    }
}


// $ipInfo = $request->getAttribute('IpInfo');
// if ($ipInfo) {
//     $ip = $ipInfo['IpAddress'];
//     $count = $ipInfo['RequestCount'];
//     $last = $ipInfo['LastRequestAt'];
//     // use it for logging, monitoring, etc.
// }