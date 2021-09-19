<?php

namespace Tests\Feature\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUsers()
    {
        $response = $this->get('/api/v1/users');

        $response->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]
        );
    }

    public function testCreateUser()
    {
        $payload = [
            'name' => 'name',
            'email' => 'email@example.com',
            'password' => '123'
        ];

        $this->json('post', 'api/v1/users/create', $payload)
        ->assertStatus(Response::HTTP_CREATED)
        ->assertJsonStructure(
            [
                'token',
                'token_type'
            ]
        )
        ->assertJsonFragment(
            [
                'token_type' => 'Bearer'
            ]
        );
    }

    public function testLogin()
    {
        Sanctum::actingAs(
            $user = User::factory()->create([
                'name' => 'test',
                'email' => 'email@example.com',
                'password' => Hash::make(123),
            ]),
        );
        $payload = [
            'email' => $user->email,
            'password' => '123',
        ];

        $this->json('post', 'api/v1/login', $payload)
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure(
            [
                'token',
                'token_type'
            ]
        )
        ->assertJsonFragment(
            [
                'token_type' => 'Bearer'
            ]
        );
    }

    public function testWrongEmailLogin()
    {
        Sanctum::actingAs(
            $user = User::factory()->create([
                'name' => 'test',
                'email' => 'email@example.com',
                'password' => Hash::make(123),
            ]),
        );
        $payload = [
            'email' => $user->email . 'foo',
            'password' => '123',
        ];

        $this->json('post', 'api/v1/login', $payload)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJson(
            [
                'errors' => [
                    'email' => [
                        "User not found"
                    ],
                ]
            ]
        );
    }

    public function testWrongPasswordLogin()
    {
        Sanctum::actingAs(
            $user = User::factory()->create([
                'name' => 'test',
                'email' => 'email@example.com',
                'password' => Hash::make(123),
            ]),
        );
        $payload = [
            'email' => $user->email,
            'password' => '321',
        ];

        $this->json('post', 'api/v1/login', $payload)
        ->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJson(
            [
                'errors' => ["incorrect password"]
            ]
        );
    }


    public function testEmptyLogin()
    {
        Sanctum::actingAs(
            $user = User::factory()->create([
                'name' => 'test',
                'email' => 'email@example.com',
                'password' => Hash::make(123),
            ]),
        );
        $payload = [
            'email' => '',
            'password' => '',
        ];

        $this->json('post', 'api/v1/login', $payload)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJson(
            [
                'errors' => [
                    'email' => [
                        "The email field is required."
                    ],
                    'password' => [
                        "The password field is required."
                    ]
                ]
            ]
        );
    }
}
