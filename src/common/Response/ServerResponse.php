<?php

declare(strict_types=1);

namespace Common\Response;

use Psr\Log\LoggerInterface;
use Spatial\Core\App;

/**
 * ServerResponse
 * 
 * Standardized API response with success/error handling, pagination, and logging.
 * 
 * @package Common\Response
 */
class ServerResponse
{
    public bool $success = true;
    public string $message = '';
    public mixed $data = null;
    public ?ErrorResponse $error = null;
    public Meta $meta;
    
    private ?LoggerInterface $logger;

    public function __construct()
    {
        $this->meta = new Meta();
        try {
            $this->logger = App::diContainer()->get(LoggerInterface::class);
        } catch (\Exception $e) {
            $this->defaultLogger();
        }
    }

    /**
     * Fallback logger if DI fails.
     */
    private function defaultLogger(): void
    {
        $this->logger = new class implements LoggerInterface {
            public function emergency($message, array $context = []): void { error_log('EMERGENCY: ' . $message); }
            public function alert($message, array $context = []): void { error_log('ALERT: ' . $message); }
            public function critical($message, array $context = []): void { error_log('CRITICAL: ' . $message); }
            public function error($message, array $context = []): void { error_log('ERROR: ' . $message); }
            public function warning($message, array $context = []): void { error_log('WARNING: ' . $message); }
            public function notice($message, array $context = []): void { error_log('NOTICE: ' . $message); }
            public function info($message, array $context = []): void { error_log('INFO: ' . $message); }
            public function debug($message, array $context = []): void { error_log('DEBUG: ' . $message); }
            public function log($level, $message, array $context = []): void { error_log(strtoupper($level) . ': ' . $message); }
        };
    }

    /**
     * Log an error and set success to false.
     */
    public function logError(string $code, mixed $detail): void
    {
        $this->success = false;
        $this->error = new ErrorResponse();
        $this->error->code = $code;
        $this->error->details = $detail;

        $this->logger->error('API Error occurred', [
            'error_code' => $code,
            'error_details' => $detail,
            'response_status' => $this->getResponseStatus(),
            'timestamp' => time()
        ]);
    }

    /**
     * Set pagination metadata.
     */
    public function paginate(int $currentPage, int $pageSize, int $totalPages, int $totalItems): void
    {
        $this->meta->pagination = new Pagination();
        $this->meta->pagination->currentPage = $currentPage;
        $this->meta->pagination->pageSize = $pageSize;
        $this->meta->pagination->totalPages = $totalPages;
        $this->meta->pagination->totalItems = $totalItems;

        $this->logger->debug('Pagination applied', [
            'current_page' => $currentPage,
            'page_size' => $pageSize,
            'total_pages' => $totalPages,
            'total_items' => $totalItems
        ]);
    }

    /**
     * Get HTTP status code based on response state.
     */
    public function getResponseStatus(): int
    {
        if ($this->success) {
            return 200;
        }
        if ($this->error === null) {
            return 500;
        }
        return (int)($this->error->code ?? 500);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        try {
            $jsonString = json_encode($this, JSON_THROW_ON_ERROR, 512);
            $this->logger->debug('Response generated successfully', [
                'success' => $this->success,
                'response_status' => $this->getResponseStatus(),
                'response_size' => strlen($jsonString)
            ]);
        } catch (\JsonException $e) {
            $jsonString = '{"success":false,"error":{"code":500,"details":"Internal server error"}}';
            $this->logger->error('Failed to encode response to JSON', [
                'exception' => $e->getMessage()
            ]);
        }

        return $jsonString;
    }
}

/**
 * ErrorResponse
 */
class ErrorResponse
{
    public string $code;
    public mixed $details;
}

/**
 * Meta
 */
class Meta
{
    public ?Pagination $pagination = null;
}

/**
 * Pagination
 */
class Pagination
{
    public int $currentPage;
    public int $pageSize;
    public int $totalPages;
    public int $totalItems;
}
