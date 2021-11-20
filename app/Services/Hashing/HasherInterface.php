<?php

namespace App\Services\Hashing;

interface HasherInterface
{
    public function hash(string $plain): string;
    public function check(string $plain, string $hashed): bool;
}
