<?php
/**
 * This file is part of Lcobucci\JWT, a simple library to handle JWT and JWS
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

declare(strict_types=1);

namespace Lcobucci\JWT\Token;

use DateTimeImmutable;
use Lcobucci\Jose\Parsing;
use Lcobucci\JWT\Builder as BuilderInterface;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;

/**
 * This class makes easier the token creation process
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 0.1.0
 */
final class Builder implements BuilderInterface
{
    /**
     * The token header
     *
     * @var array
     */
    private $headers = ['typ'=> 'JWT', 'alg' => 'none'];

    /**
     * The token claim set
     *
     * @var array
     */
    private $claims = [];

    /**
     * The data encoder
     *
     * @var Parsing\Encoder
     */
    private $encoder;

    /**
     * Initializes a new builder
     */
    public function __construct(Parsing\Encoder $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * {@inheritdoc}
     */
    public function permittedFor(string $audience): BuilderInterface
    {
        $audiences = $this->claims[RegisteredClaims::AUDIENCE] ?? [];

        if (!in_array($audience, $audiences)) {
            $audiences[] = $audience;
        }

        return $this->setClaim(RegisteredClaims::AUDIENCE, $audiences);
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAt(DateTimeImmutable $expiration): BuilderInterface
    {
        return $this->setClaim(RegisteredClaims::EXPIRATION_TIME, $expiration);
    }

    /**
     * {@inheritdoc}
     */
    public function identifiedBy(string $id): BuilderInterface
    {
        return $this->setClaim(RegisteredClaims::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function issuedAt(DateTimeImmutable $issuedAt): BuilderInterface
    {
        return $this->setClaim(RegisteredClaims::ISSUED_AT, $issuedAt);
    }

    /**
     * {@inheritdoc}
     */
    public function issuedBy(string $issuer): BuilderInterface
    {
        return $this->setClaim(RegisteredClaims::ISSUER, $issuer);
    }

    /**
     * {@inheritdoc}
     */
    public function canOnlyBeUsedAfter(DateTimeImmutable $notBefore): BuilderInterface
    {
        return $this->setClaim(RegisteredClaims::NOT_BEFORE, $notBefore);
    }

    /**
     * {@inheritdoc}
     */
    public function relatedTo(string $subject): BuilderInterface
    {
        return $this->setClaim(RegisteredClaims::SUBJECT, $subject);
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader(string $name, $value): BuilderInterface
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withClaim(string $name, $value): BuilderInterface
    {
        if (in_array($name, RegisteredClaims::ALL, true)) {
            throw new \InvalidArgumentException('You should use the correct methods to set registered claims');
        }

        return $this->setClaim($name, $value);
    }

    private function setClaim(string $name, $value): BuilderInterface
    {
        $this->claims[$name] = $value;

        return $this;
    }

    private function encode(array $items): string
    {
        return $this->encoder->base64UrlEncode(
            $this->encoder->jsonEncode($items)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getToken(Signer $signer, Key $key): Plain
    {
        $headers = $this->headers;
        $headers['alg'] = $signer->getAlgorithmId();

        $encodedHeaders = $this->encode($headers);
        $encodedClaims = $this->encode($this->formatClaims($this->claims));

        $signature = $signer->sign($encodedHeaders . '.' . $encodedClaims, $key);
        $encodedSignature = $this->encoder->base64UrlEncode($signature);

        return new Plain(
            new DataSet($headers, $encodedHeaders),
            new DataSet($this->claims, $encodedClaims),
            new Signature($signature, $encodedSignature)
        );
    }

    private function formatClaims(array $claims): array
    {
        if (isset($claims[RegisteredClaims::AUDIENCE][0]) && !isset($claims[RegisteredClaims::AUDIENCE][1])) {
            $claims[RegisteredClaims::AUDIENCE] = $claims[RegisteredClaims::AUDIENCE][0];
        }

        foreach (array_intersect(RegisteredClaims::DATE_CLAIMS, array_keys($claims)) as $claim) {
            $claims[$claim] = $this->convertDate($claims[$claim]);
        }

        return $claims;
    }

    /**
     * @return int|string
     */
    private function convertDate(DateTimeImmutable $date)
    {
        $seconds = $date->format('U');
        $microseconds = $date->format('u');

        if ((int) $microseconds === 0) {
            return (int) $seconds;
        }

        return $seconds . '.' . $microseconds;
    }
}
