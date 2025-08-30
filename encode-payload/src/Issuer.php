<?php

namespace AsymmetricJWTKit;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class Issuer
{
    public function __construct(
        protected Config $config,
    ) {
    }

    public function createToken(array $claims = []): string
    {
        [$x5Certificates, $entityPrivateKey] = $this->generateCertChain();

        return $this->generateSignedPayload($entityPrivateKey, $x5Certificates, $claims);
    }

    protected function generateCertChain(): array
    {
        [$rootPKey, $rootCsrSign, $rootPem] = $this->generateX509Cert();

        [$intermediatePKey, $intermediateCsrSign, $intermediatePem] = $this->generateX509Cert($rootCsrSign, $rootPKey);

        [$entityPKey, $entityCsrSign, $entityPem] = $this->generateX509Cert($intermediateCsrSign, $intermediatePKey);

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

    protected function generateSignedPayload(string $privateKey, array $x5cChain, array $claims = []): string
    {
        $config = Configuration::forAsymmetricSigner(
            signer: new Sha256(),
            signingKey: InMemory::plainText($privateKey),
            verificationKey: InMemory::plainText($x5cChain[0]),
        );

        return $config->builder()
            ->withClaim('data', $claims)
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

        $csr = openssl_csr_new($this->config->getOwnerInformation(), $pkey);

        $csrSign = openssl_csr_sign($csr, $prevCACert, $prevPKey ?? $pkey, 365);

        openssl_x509_export($csrSign, $pem);

        return [$pkey, $csrSign, $pem];
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
