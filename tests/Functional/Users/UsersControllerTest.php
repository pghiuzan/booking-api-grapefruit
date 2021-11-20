<?php

namespace Tests\Functional\Users;

use App\Models\User;
use Illuminate\Http\Response;
use Tests\FunctionalTestCase;

class UsersControllerTest extends FunctionalTestCase
{
    private const ENDPOINT = '/users';

    public function setUp(): void
    {
        parent::setUp();

        // testing the API key auth or others things like that is not the job of this test
        $this->withoutMiddleware();
    }

    public function testListingUsers()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $this->json('GET', $this->createUri(self::ENDPOINT, ['perPage' => 2]));
        $this->assertResponseOk();

        $response = json_decode($this->response->getContent(), true);
        $this->assertSame(2, count($response['data']));
        $this->assertSame($user1->id, $response['data'][0]['id']);
        $this->assertSame($user2->id, $response['data'][1]['id']);

        $this->assertSame(1, $response['meta']['current_page']);
        $this->assertSame('2', $response['meta']['per_page']);
        $this->assertSame(3, $response['meta']['total']);

        $this->json('GET', $this->createUri(self::ENDPOINT, ['perPage' => 2, 'page' => 2]));
        $this->assertResponseOk();

        $response = json_decode($this->response->getContent(), true);
        $this->assertSame(1, count($response['data']));
        $this->assertSame($user3->id, $response['data'][0]['id']);

        $this->json('GET', $this->createUri(self::ENDPOINT, ['perPage' => 2, 'page' => 26]));
        $this->assertResponseOk();

        $response = json_decode($this->response->getContent(), true);
        $this->assertSame(0, count($response['data']));
    }

    public function testGettingOneUser()
    {
        $user = User::factory()->create();

        $this->json('GET', sprintf('%s/%d', self::ENDPOINT, $user->id));
        $this->assertResponseOk();
        $this->seeJson([
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
        ]);
    }

    public function testGettingOneUserThrowsNotFoundExceptionWhenMissinUser()
    {
        $this->json('GET', sprintf('%s/%d', self::ENDPOINT, 999999));
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function testCreateUser()
    {
        $this->json('POST', self::ENDPOINT, [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@grapefruit.ro',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);
        $this->assertResponseStatus(Response::HTTP_CREATED);
        $this->seeJson([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@grapefruit.ro',
        ]);
        $this->seeInDatabase('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@grapefruit.ro',
        ]);
    }

    /**
     * @dataProvider invalidUsersDataProvider
     */
    public function testCreateUserWithInvalidData(array $userData, array $expectedErrors)
    {
        $this->json('POST', self::ENDPOINT, $userData);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonContains(['errors' => $expectedErrors]);
    }

    public function invalidUsersDataProvider(): array
    {
        return [
            [
                [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'email' => 'john.doe@grapefruit.ro',
                    'password' => '12345678',
                ],
                [
                    'password' => ['The password confirmation does not match.'],
                ],
            ],
            [
                [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'email' => 'john.doe@grapefruit.ro',
                    'password' => '12345678',
                    'password_confirmation' => '123456789',
                ],
                [
                    'password' => ['The password confirmation does not match.'],
                ],
            ],
            [
                [
                    'last_name' => 'Doe',
                    'email' => 'john.doe@grapefruit.ro',
                    'password' => '12345678',
                    'password_confirmation' => '12345678',
                ],
                [
                    'first_name' => ['The first name field is required.'],
                ],
            ],
            [
                [
                    'first_name' => 'John',
                    'email' => 'john.doe@grapefruit.ro',
                    'password' => '12345678',
                    'password_confirmation' => '12345678',
                ],
                [
                    'last_name' => ['The last name field is required.'],
                ],
            ],
            [
                [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'password' => '12345678',
                    'password_confirmation' => '12345678',
                ],
                [
                    'email' => ['The email field is required.'],
                ],
            ],
            [
                [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'email' => 'invalid-email',
                    'password' => '12345678',
                    'password_confirmation' => '12345678',
                ],
                [
                    'email' => ['The email must be a valid email address.'],
                ],
            ],
        ];
    }

    public function testCreateUserWithExistingEmail()
    {
        $user = User::factory()->create();

        $this->json('POST', self::ENDPOINT, [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $user->email,
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonContains(['errors' => [
            'email' => [sprintf('User with %s email already exists.', $user->email)],
        ]]);
    }

    public function testUpdateUser()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->json('PATCH', sprintf('%s/%d', self::ENDPOINT, $user->id), [
            'first_name' => 'Jane',
            'last_name' => 'Eod',
        ]);
        $this->assertResponseOk();
        $this->seeJson([
            'first_name' => 'Jane',
            'last_name' => 'Eod',
            'email' => $user->email,
        ]);
        $this->seeInDatabase('users', [
            'id' => $user->id,
            'first_name' => 'Jane',
            'last_name' => 'Eod',
            'email' => $user->email,
        ]);
    }
}
