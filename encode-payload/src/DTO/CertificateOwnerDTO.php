<?php

namespace AsymmetricJWTKit\DTO;

final readonly class CertificateOwnerDTO
{
    public function __construct(
        public string $countryName,
        public string $stateOrProvinceName,
        public string $localityName,
        public string $organizationName,
        public string $organizationalUnitName,
        public string $commonName,
        public string $email,
    ) {
    }

    public function getOwnerInformation(): array
    {
        return [
            'countryName' =>  $this->countryName,
            'stateOrProvinceName' => $this->stateOrProvinceName,
            'localityName' => $this->localityName,
            'organizationName' => $this->organizationName,
            'organizationalUnitName' => $this->organizationalUnitName,
            'commonName' => $this->commonName,
            'emailAddress' => $this->email,
        ];
    }
}