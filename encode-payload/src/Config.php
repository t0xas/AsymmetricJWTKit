<?php

namespace AsymmetricJWTKit;

final readonly class Config
{
    public function __construct(
        public ?string $publicKey = null,
        public ?string $privateKey = null,
        public ?string $privateKeyPassword = null,
    ) {
    }
}