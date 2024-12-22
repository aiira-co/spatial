<?php

declare(strict_types=1);
namespace Common\Interfaces;

interface JwtInterface
{
    /**
     * @param int $userId
     * @param array|null $extraIds
     * @return string
     */
    public function genToken(int $userId, ?array $extraIds): string;

    /**
     * @param mixed $token
     * @param string $signature
     * @return bool
     */
    public function validateToken(mixed $token, string $signature): bool;
}
