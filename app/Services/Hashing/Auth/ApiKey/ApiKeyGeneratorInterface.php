<?php

namespace App\Services\Hashing\Auth\ApiKey;

interface ApiKeyGeneratorInterface
{
    public function generateKey(): string;
}
