<?php

namespace App\Services\Auth\ApiKey;

use Ramsey\Uuid\Uuid;

class GenericApiKeyGenerator implements ApiKeyGeneratorInterface
{
    public function generateKey(): string
    {
        return Uuid::uuid4()->toString();
    }
}
