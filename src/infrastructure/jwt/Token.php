<?php

declare(strict_types=1);

namespace Infrastructure\JWT;

use Lcobucci\JWT\Validation\Constraint\{IssuedBy, PermittedFor, ValidAt, SignedWith};
use Lcobucci\Clock\Clock;
use  Core\Application\Interfaces\JwtInterface;

use DateTimeImmutable;

class Token implements JwtInterface
{
    private $_config;

    function __construct()
    {
        $this->_config = (new ConfigJWT())->tokenConfig();
    }



    public function genToken(int $userId): string
    {
        // var_dump($config);
        assert($this->_config instanceof \Lcobucci\JWT\Configuration);
        $now = new \DateTimeImmutable();
        $token = $this->_config->createBuilder()
            ->identifiedBy(bin2hex(random_bytes(16)))
            ->issuedBy('https://auth.air.com')
            ->permittedFor('https://app.perfecttvapp.com')
            ->permittedFor('https://client2.bar')
            ->permittedFor('https://client3.bar')
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now->modify('+30 seconds'))
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('userId', $userId)
            ->getToken($this->_config->getSigner(), $this->_config->getSigningKey());
        return (string) $token;
    }


    public function validateToken($jwt, $signature): bool
    {
        // use a blacklist to block user
        $token = $this->_config->getParse()->parse($jwt);
        $constraints = [
            new IssuedBy('https://foo.bar', 'https://bar.foo'),
            new PermittedFor('https://client1.bar'),
            new ValidAt(new ValidClock()),
            new SignedWith($this->_config->getSigner(), $this->_config->getSVerificationKey())
        ];

        return $this->_config->getValidator()->validate($token, ...$constraints);
    }
}


class ValidClock implements Clock
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
