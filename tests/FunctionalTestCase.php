<?php

namespace Tests;

use Laravel\Lumen\Testing\DatabaseTransactions;

abstract class FunctionalTestCase extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->createApplication();
    }

    protected function createUri(string $path, array $queryParams): string
    {
        return sprintf(
            '%s?%s',
            $path,
            http_build_query($queryParams)
        );
    }
}
