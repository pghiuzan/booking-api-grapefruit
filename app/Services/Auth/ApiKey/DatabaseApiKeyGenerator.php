<?php

namespace App\Services\Auth\ApiKey;

use App\Models\ApiKey;
use App\Services\Hashing\HasherInterface;

class DatabaseApiKeyGenerator implements ApiKeyGeneratorInterface
{
    public function __construct(
        private GenericApiKeyGenerator $decorated,
        private HasherInterface $hasher
    ) {
    }

    public function generateKey(): string
    {
        $key = $this->decorated->generateKey();

        ApiKey::create([
            'key' => $this->hasher->hash($key),
        ]);

        return $key;
    }
}
