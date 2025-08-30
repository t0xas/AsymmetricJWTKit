<?php

namespace AsymmetricJWTKit;

use AsymmetricJWTKit\DTO\CertificateOwnerDTO;

readonly class Config
{
    public function __construct(
        public string $pathRootSer,
        public string $pathToCASer,
        public CertificateOwnerDTO $owner,
    ) {
    }
}
