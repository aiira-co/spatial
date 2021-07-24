<?php

namespace Infrastructure\Jwt;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\LocalFileReference;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class ConfigJWT
{

    /**
     * @return \Lcobucci\JWT\Configuration
     */
    public function tokenConfig(): Configuration
    {
        return Configuration::forAsymmetricSigner(
            new Sha256(),
            LocalFileReference::file(
                'file:/' . getenv('JWT_SECRET_KEY'),
                getenv('JWT_PASS_PHRASE')
            ),
            LocalFileReference::file('file:/' . getenv('JWT_PUBLIC_KEY'))
        );
    }
}
