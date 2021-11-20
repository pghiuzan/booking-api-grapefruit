<?php

namespace App\Services\Auth\ApiKey;

interface ApiKeyGeneratorInterface
{
    public function generateKey(): string;
}
