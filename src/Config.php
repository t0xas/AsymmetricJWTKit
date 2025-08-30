<?php

namespace AsymmetricJWTKit;

use AsymmetricJWTKit\DTO\CertificateOwnerDTO;

readonly class Config
{
    public function __construct(
        public int $dayAvailable = 365,
        public CertificateOwnerDTO $owner,
    ) {
    }
}
