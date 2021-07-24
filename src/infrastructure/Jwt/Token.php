<?php

declare(strict_types=1);

namespace Infrastructure\JWT;

use DI\Container;
use Lcobucci\JWT\Validation\Constraint\{IssuedBy, PermittedFor, ValidAt, SignedWith};
use Lcobucci\Clock\Clock;
use  Core\Application\Interfaces\JwtInterface;

use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\UnencryptedToken;

class Token implements JwtInterface
{
    /**
     * @var Configuration
     */
    private Configuration $_config;
    private \Lcobucci\JWT\Token $parsedToken;

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function __construct()
    {
        $container = new Container();
        $this->_config = $container->get(ConfigJWT::class)->tokenConfig();

        assert($this->_config instanceof Configuration);
    }


    /**
     * @param int $userId
     * @param array|null $extraIds
     * @return string
     * @throws \Exception
     */
    public function genToken(int $userId, ?array $extraIds): string
    {
        // var_dump($config);
        assert($this->_config instanceof \Lcobucci\JWT\Configuration);
        $now = new \DateTimeImmutable();
        try {
            $token = $this->_config->builder()
                ->identifiedBy(
                    bin2hex(random_bytes(16))
                )
                ->issuedBy('https://aiira.co')
                ->permittedFor('https://client1.bar')
                ->permittedFor('https://client2.bar')
                ->permittedFor('https://client3.bar')
                ->issuedAt($now)
                ->canOnlyBeUsedAfter($now->modify('+30 seconds'))
                ->expiresAt($now->modify('+1 hour'))
                ->withClaim('userId', $userId)
                ->withClaim('extraClaims', $extraIds)
                ->getToken($this->_config->signer(), $this->_config->signingKey());
        } catch (\Exception $e) {
            die($e->getMessage());
        }

        return $token->toString();
    }


    /**
     * @param string $data
     * @param string $signature
     * @return bool
     */
    public function validateToken(string $data, string $signature): bool
    {
//        echo ' token to validate --> ' . $jwt;
        // use a blacklist to block user
        $this->parsedToken = $this->_config->parser()->parse($data);
        assert($this->parsedToken instanceof UnencryptedToken);
        $this->_config->setValidationConstraints(
            new IssuedBy('https://aiira.co'),
//            new PermittedFor('https://client1.bar'),
//            new PermittedFor('https://client2.bar'),
//            new PermittedFor('https://client3.bar'),
            new SignedWith($this->_config->signer(), $this->_config->verificationKey()),
        );

        $constraints = $this->_config->validationConstraints();
//        return true;
        return $this->_config->validator()->validate($this->parsedToken, ...$constraints);
    }

    /**
     * @return \Lcobucci\JWT\Token
     */
    public function getParseToken(): \Lcobucci\JWT\Token
    {
        return $this->parsedToken;
    }
}
