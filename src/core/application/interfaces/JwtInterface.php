<?php

namespace Core\Application\Interfaces;

interface JwtInterface
{
    /**
     * @param int $userId
     * @param array|null $extraIds
     * @return string
     */
    public function genToken(int $userId, ?array $extraIds): string;

    /**
     * @param string $data
     * @param string $signature
     * @return bool
     */
    public function validateToken(string $data, string $signature): bool;
}
