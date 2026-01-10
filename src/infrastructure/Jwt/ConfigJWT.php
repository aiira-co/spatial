<?php

declare(strict_types=1);

namespace Infrastructure\JWT;

use Common\Libraries\SystemClock;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Spatial\Core\Attributes\Injectable;
use Psr\Log\LoggerInterface;

#[Injectable]
class ConfigJWT
{
    private Configuration $configuration;

    public function __construct(private ?LoggerInterface $logger = null)
    {
        $this->configuration = $this->buildConfig();
    }

    private function buildConfig(): Configuration
    {
        $secretKey = getenv('JWT_SECRET_KEY') ?: '/secrets/private.pem';
        $publicKey = getenv('JWT_PUBLIC_KEY') ?: '/secrets/public.pem';
        $passphrase = getenv('JWT_PASSPHRASE') ?: null;

        return Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file($secretKey),
            InMemory::file($publicKey)
        );
    }

    public function configuration(): Configuration
    {
        return $this->configuration;
    }

    public function parseToken(string $jwt): ?Token
    {
        try {
            return $this->configuration->parser()->parse($jwt);
        } catch (CannotDecodeContent|InvalidTokenStructure|UnsupportedHeaderFound $e) {
            $this->logger?->error('JWT parse failed: ' . $e->getMessage());
            return null;
        }
    }

    public function validator(): \Lcobucci\JWT\Validator
    {
        return $this->configuration->validator();
    }

    public function constraints(): array
    {
        return [
            new IssuedBy(getenv('JWT_ISSUER')),
            new PermittedFor(getenv('JWT_AUDIENCE')),
            new StrictValidAt(new SystemClock()),
        ];
    }
}
