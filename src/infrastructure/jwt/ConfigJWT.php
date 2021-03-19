<?php

namespace Infrastructure\Jwt;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class ConfigJWT
{

    public function __construct()
    { }

    public function tokenConfig()
    {
        return Configuration::forAsymmetricSigner(
            new Sha256(),
            new Key('file://../id_rsa', 'glory'),
            new Key('file://../id_rsa.pub')
        );
    }
}
