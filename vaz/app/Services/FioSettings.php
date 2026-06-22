<?php

namespace App\Service;

class FioSettings
{
    public function __construct(
        public string $token,
        public string $token2,
        public string $account,
        public string $bankId,
        public string $iban,
    ) {}
}