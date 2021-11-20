<?php

namespace App\Providers;

use App\Services\Hashing\Auth\ApiKey\ApiKeyGeneratorInterface;
use App\Services\Hashing\Auth\ApiKey\DatabaseApiKeyGenerator;
use App\Services\Hashing\Sha256Hasher;
use App\Services\Hashing\HasherInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(HasherInterface::class, Sha256Hasher::class);
        $this->app->bind(ApiKeyGeneratorInterface::class, DatabaseApiKeyGenerator::class);
    }
}
