<?php

namespace AsymmetricJWTKit;

use Exception;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use phpseclib3\File\X509;

class Validator
{
    public function validate(string $token, string $rootCACertContent): bool
    {
        try {
            $parsedToken = (new Parser(new JoseEncoder()))->parse($token);
        } catch (Exception) {
            return false;
        }

        $x5CertChain = $parsedToken->headers()->get('x5c');

        if (empty($x5CertChain) || !$this->isValidCertChain($x5CertChain, $rootCACertContent)) {
           return false;
        }

        $publicKey = $this->wrapCertificate($x5CertChain[0]);

        $config = Configuration::forAsymmetricSigner(new Sha256(), InMemory::plainText('empty'), InMemory::plainText($publicKey));

        $singer = new SignedWith($config->signer(), $config->verificationKey());

        return $config->validator()->validate($parsedToken, $singer);
    }

    protected function isValidCertChain(array $x5CertChain, string $rootCACertContent): bool
    {
        foreach ($x5CertChain  as $index => $cert) {
            $currentCert = $this->wrapCertificate($cert);

            $nextCert = !empty($x5CertChain[$index + 1])
                ? $this->wrapCertificate($x5CertChain[$index + 1])
                : $rootCACertContent;

            $x509 = new X509();
            $x509->loadCA($nextCert);
            $x509->loadX509($currentCert);

            if (!$x509->validateSignature()) {
               return false;
            }
        }

        return true;
    }

    private static function wrapCertificate(string $cert): string
    {
        return <<<EOD
        -----BEGIN CERTIFICATE-----
        $cert
        -----END CERTIFICATE-----
        EOD;
    }
}