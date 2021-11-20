<?php

namespace App\Services\Hashing;

class Sha256Hasher implements HasherInterface
{
    private const ALGO = 'SHA256';

    private function hashValue(string $input): string
    {
        return hash(self::ALGO, $input);
    }

    public function hash(string $plain): string
    {
        return $this->hashValue($plain);
    }

    public function check(string $plain, string $hashed): bool
    {
        return $this->hashValue($plain) === $hashed;
    }
}
