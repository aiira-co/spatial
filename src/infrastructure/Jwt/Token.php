<?php

declare(strict_types=1);

namespace Infrastructure\JWT;

use Common\Interfaces\JwtInterface;
use Common\Libraries\SystemClock;
use DateTimeImmutable;
use Exception;
use Infrastructure\RedisServices\RedisDenylistStorage;
use Lcobucci\JWT\Token as JwtToken;
use Lcobucci\JWT\Validation\Constraint\{
    IssuedBy,
    PermittedFor,
    SignedWith,
    StrictValidAt
};
use Lcobucci\Clock;
use Lcobucci\JWT\Token\Plain;
use Spatial\Core\Attributes\Injectable;

#[Injectable]
class Token implements JwtInterface
{
    public function __construct(
        private ConfigJWT $_jwtConfig,
        private RedisDenylistStorage $denylist // e.g. Redis or in-memory
    ) {}

    public function genToken(int $userId, ?array $extraIds, ?string $audience = ''): string
    {
        $now = new DateTimeImmutable();
        $jti = bin2hex(random_bytes(16));

        $token = $this->_jwtConfig->configuration()->builder()
            ->issuedBy(getenv('JWT_ISSUER'))
            ->permittedFor($audience ?: getenv('JWT_AUDIENCE'))
            ->identifiedBy($jti)
            ->issuedAt($now)
            ->expiresAt($now->modify('+6 months'))
            ->withClaim('userId', $userId)
            ->withClaim('appClaim', $extraIds)
            ->getToken(
                $this->_jwtConfig->configuration()->signer(),
                $this->_jwtConfig->configuration()->signingKey()
            );

        return $token->toString();
    }

    public function validateToken(JwtToken $token): bool
    {
        $clock = new SystemClock(); // current time

        $validationCnfig = $this->_jwtConfig->configuration()->withValidationConstraints(
            new IssuedBy(getenv('JWT_ISSUER')),
            new PermittedFor(getenv('JWT_AUDIENCE')),
            new SignedWith(
                $this->_jwtConfig->configuration()->signer(),
                $this->_jwtConfig->configuration()->verificationKey()
            ),
            new StrictValidAt($clock)
        );

        return $this->_jwtConfig->configuration()->validator()
            ->validate($token, ...$validationCnfig->validationConstraints());
    }

    public function getParseToken(string $data): ?Plain
    {
        try {
            return $this->_jwtConfig->parseToken($data);
        } catch (Exception) {
            return null;
        }
    }

    public function isRevoked(string $jti): bool
    {
        return $this->denylist->exists($jti);
    }

    public function revokeToken(string $jti, int $ttl): void
    {
        $this->denylist->add($jti, $ttl); // store until expiration
    }
}
