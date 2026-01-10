<?php

declare(strict_types=1);

namespace Presentation\WebApi\Controllers;

use Psr\Http\Message\ResponseInterface;
use Spatial\Core\Attributes\ApiController;
use Spatial\Core\Attributes\Route;
use Spatial\Core\ControllerBase;
use Spatial\Common\HttpAttributes\HttpGet;

/**
 * Health Check Controller
 * 
 * Provides health check endpoints for monitoring and container orchestration.
 * 
 * @package Presentation\WebApi\Controllers
 */
#[ApiController]
#[Route('[controller]')]
class HealthController extends ControllerBase
{
    private float $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    /**
     * Basic health check - returns 200 if app is running.
     * 
     * GET /health
     */
    #[HttpGet]
    public function index(): ResponseInterface
    {
        return $this->ok([
            'status' => 'healthy',
            'timestamp' => date('c'),
        ]);
    }

    /**
     * Detailed health check with system info.
     * 
     * GET /health/details
     */
    #[HttpGet('details')]
    public function details(): ResponseInterface
    {
        $uptime = microtime(true) - $this->startTime;

        return $this->ok([
            'status' => 'healthy',
            'timestamp' => date('c'),
            'version' => '4.0.0-RC3',
            'php_version' => PHP_VERSION,
            'memory' => [
                'usage' => $this->formatBytes(memory_get_usage(true)),
                'peak' => $this->formatBytes(memory_get_peak_usage(true)),
            ],
            'uptime_seconds' => round($uptime, 2),
            'environment' => defined('AppConfig') && AppConfig['enableProdMode'] 
                ? 'production' 
                : 'development',
        ]);
    }

    /**
     * Liveness probe for Kubernetes.
     * 
     * GET /health/live
     */
    #[HttpGet('live')]
    public function live(): ResponseInterface
    {
        return $this->ok(['status' => 'alive']);
    }

    /**
     * Readiness probe for Kubernetes.
     * 
     * GET /health/ready
     */
    #[HttpGet('ready')]
    public function ready(): ResponseInterface
    {
        // Check dependencies here (database, cache, etc.)
        $checks = [
            'app' => true,
            // 'database' => $this->checkDatabase(),
            // 'cache' => $this->checkCache(),
        ];

        $allReady = !in_array(false, $checks, true);

        if (!$allReady) {
            return $this->json([
                'status' => 'not_ready',
                'checks' => $checks,
            ], 503);
        }

        return $this->ok([
            'status' => 'ready',
            'checks' => $checks,
        ]);
    }

    /**
     * Format bytes to human readable string.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen((string)$bytes) - 1) / 3);
        return sprintf('%.2f %s', $bytes / pow(1024, $factor), $units[(int)$factor]);
    }
}
