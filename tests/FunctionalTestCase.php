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
}
