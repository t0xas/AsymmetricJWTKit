<?php

namespace Tests;

use AsymmetricJWTKit\Config;
use AsymmetricJWTKit\DTO\CertificateOwnerDTO;
use AsymmetricJWTKit\Issuer;
use PHPUnit\Framework\TestCase;

class IssuerTest extends TestCase
{
    public function testEncode()
    {
        $config = new Config(
            pathRootSer: __DIR__ . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'fixtures',
            pathToCASer: __DIR__ . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'fixtures',
            owner: new CertificateOwnerDTO(
                countryName: 'US',
                stateOrProvinceName: 'CA',
                localityName: 'San Francisco',
                organizationName: 'Test Organization',
                organizationalUnitName: 'Test Organization Unit Name',
                commonName: 'John Doe',
                email: 'test@example.com',
            ),
        );

        $token = (new Issuer($config))->createToken();
    }
}