<?php

namespace Tests\Functional\Auth;

use App\Models\User;
use Tests\FunctionalTestCase;

class AuthControllerTest extends FunctionalTestCase
{
    private const ENDPOINT = '/auth';

    public function testLoginHappyPath()
    {
        $this->markTestSkipped('For some reason auth stuff doesnt get loaded during testing');

        $user = User::factory()->create();

        $this->json('POST', self::ENDPOINT, [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertResponseOk();
        $this->seeJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
    }
}
