<?php
/**
 * This file is part of Lcobucci\JWT, a simple library to handle JWT and JWS
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

declare(strict_types=1);

namespace Lcobucci\JWT;

use DateTimeImmutable;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token\Plain;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 4.0.0
 */
interface Builder
{
    /**
     * Appends a new audience
     */
    public function permittedFor(string $audience): Builder;

    /**
     * Configures the expiration time
     */
    public function expiresAt(DateTimeImmutable $expiration): Builder;

    /**
     * Configures the token id
     */
    public function identifiedBy(string $id): Builder;

    /**
     * Configures the time that the token was issued
     */
    public function issuedAt(DateTimeImmutable $issuedAt): Builder;

    /**
     * Configures the issuer
     */
    public function issuedBy(string $issuer): Builder;

    /**
     * Configures the time before which the token cannot be accepted
     */
    public function canOnlyBeUsedAfter(DateTimeImmutable $notBefore): Builder;

    /**
     * Configures the subject
     */
    public function relatedTo(string $subject): Builder;

    /**
     * Configures a header item
     */
    public function withHeader(string $name, $value): Builder;

    /**
     * Configures a claim item
     *
     * @throws \InvalidArgumentException When trying to set a registered claim
     */
    public function withClaim(string $name, $value): Builder;

    /**
     * Returns a signed token to be used
     */
    public function getToken(Signer $signer, Key $key): Plain;
}
