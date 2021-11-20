<?php

namespace Tests\Functional\Auth;

use App\Services\Auth\ApiKey\ApiKeyGeneratorInterface;
use Illuminate\Http\Response;
use Tests\FunctionalTestCase;

class ApiKeyAuthenticationTest extends FunctionalTestCase
{
    private const ENDPOINT = '/health-check';

    public function testGettingUnauthorizedErrorWhenNotAuthenticated()
    {
        $this->get(self::ENDPOINT);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJson([
            'status' => 'error',
            'code' => 401,
            'message' => 'No API key provided',
        ]);
    }

    public function testGettingUnauthorizedErrorWithWrongKey()
    {
        $this->get(self::ENDPOINT, [
            'Api-Key' => 'some-random-key',
        ]);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
        $this->seeJson([
            'status' => 'error',
            'code' => 401,
            'message' => 'Invalid API key provided',
        ]);
    }

    public function testHappyPath()
    {
        /** @var ApiKeyGeneratorInterface $keyGenerator */
        $keyGenerator = $this->app->make(ApiKeyGeneratorInterface::class);
        $key = $keyGenerator->generateKey();

        $this->get(self::ENDPOINT, [
            'Api-Key' => $key,
        ]);
        $this->assertResponseOk();
    }
}
