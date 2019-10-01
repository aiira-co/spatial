<?php
namespace Core\Application\Interfaces;

interface JwtInterface
{
    public function genToken(int $userId): string;

    public function validateToken($data, $signature): bool;
}
