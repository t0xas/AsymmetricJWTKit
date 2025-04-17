<?php

namespace AsymmetricJWTKit;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class Encode
{
    public function __construct(
        protected Config $config,
    ) {
    }

    public function createToken(array $data = []): string
    {
        list($x5Certificates, $entityPrivateKey) = $this->generateCertChain();

        return $this->generateSignedPayload($entityPrivateKey, $x5Certificates, $data);
    }

    protected function generateCertChain(): array
    {
        list($rootPKey, $rootCsrSign, $rootPem) = $this->generateX509Cert();

        list($intermediatePKey, $intermediateCsrSign, $intermediatePem) = $this->generateX509Cert(
            $rootCsrSign,
            $rootPKey
        );

        list($entityPKey, $entityCsrSign, $entityPem) = $this->generateX509Cert(
            $intermediateCsrSign,
            $intermediatePKey
        );

        openssl_pkey_export($entityPKey, $entityPrivateKeyOut);

        return [
            [
                $entityPem,
                $intermediatePem,
                $rootPem,
            ],
            $entityPrivateKeyOut,
        ];
    }

    protected function generateSignedPayload(string $privateKey, array $x5cChain, array $data = []): string
    {
        $config = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::plainText($privateKey),
            InMemory::plainText($x5cChain[0]),
        );

        return $config->builder()
            ->withClaim('data', $data)
            ->withHeader('x5c', $this->prepareX5CHeader($x5cChain))
            ->getToken($config->signer(), $config->signingKey())
            ->toString();
    }

    protected function generateX509Cert($prevCACert = null, $prevPKey = null): array
    {
        $pkey = openssl_pkey_new([
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name' => 'prime256v1',
        ]);

        $csr = openssl_csr_new(
            distinguished_names: [
                'countryName' => 'US',
                'stateOrProvinceName' => 'California',
                'localityName' => 'San Francisco',
                'organizationName' => 'Example Corp',
                'organizationalUnitName' => 'Development',
                'commonName' => 'example.com',
                'emailAddress' => 'email@example.com',
            ],
            private_key: $pkey,
        );

        $csrSign = openssl_csr_sign($csr, $prevCACert, $prevPKey ?? $pkey, 365);

        openssl_x509_export($csrSign, $pem);

        return [
            $pkey,
            $csrSign,
            $pem,
        ];
    }

    protected function prepareX5CHeader(array $x5Certs): array
    {
        return array_map(
            callback: fn ($cert) => str_replace(
                search: [
                    '-----BEGIN CERTIFICATE-----',
                    '-----END CERTIFICATE-----',
                    "\n",
                    "\r",
                ],
                replace: '',
                subject: $cert,
            ),
            array: $x5Certs
        );
    }

}