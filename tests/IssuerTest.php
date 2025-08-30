<?php

namespace Tests;

use AsymmetricJWTKit\Config;
use AsymmetricJWTKit\DTO\CertificateOwnerDTO;
use AsymmetricJWTKit\Issuer;
use AsymmetricJWTKit\Validator;

class IssuerTest extends TestCase
{
    public function testEncode()
    {
        $issuer = new Issuer(
            config: new Config(
                dayAvailable: 120,
                owner: new CertificateOwnerDTO(
                    countryName: 'US',
                    stateOrProvinceName: 'CA',
                    localityName: 'San Francisco',
                    organizationName: 'Test Organization',
                    organizationalUnitName: 'Test Organization Unit Name',
                    commonName: 'John Doe',
                    email: 'test@example.com',
                ),
            )
        );

        $token = $issuer->createToken();
        $publicRootCert = $issuer->getPublicCertificate();

        $isValid = (new Validator())->validate($token, $publicRootCert);

        $this->assertTrue($isValid);
    }
}