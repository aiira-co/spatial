<?php namespace Presentation\IdentityApi\Models;

use Lcobucci\JWT\Builder;

class TokenModel
{
    public function genTokenOLD(int $id): string
    {
        $signer = new Sha256();
        $token = (new Builder())->setIssuer('JWT Example')
            ->setAudience('JWT Example')
            ->setId('4f1g23a12aa', true)
            ->setIssuedAt(time())
            ->setExpiration(time() + 2592000) //for 30 days
            ->set('uid', $id) // Configures a new claim, called "uid"
            ->sign($signer, 'godnation') // creates a signature using "testing" as key
            ->getToken();
        return (string) $token;
    }

    public function genToken(array $user): string
    {
        $config = (new ConfigJWT)->tokenConfig();
        // var_dump($config);
        assert($config instanceof \Lcobucci\JWT\Configuration);
        $now = new \DateTimeImmutable();
        $token = $config->createBuilder()
            ->identifiedBy(bin2hex(random_bytes(16)))
            ->issuedBy('https://auth.air.com')
            ->permittedFor('https://app.perfecttvapp.com')
            ->permittedFor('https://client2.bar')
            ->permittedFor('https://client3.bar')
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now->modify('+30 seconds'))
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('userId', $user)
            ->getToken($config->getSigner(), $config->getSigningKey());
        return (string) $token;
    }
}
