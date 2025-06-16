<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register(): void
    {
        $response = $this->postJson('api/register', [
            'name' => 'Test User',
            'last_name' => 'Doe',
            'phone' => '+481234567890',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'last_name' => 'Doe',
            'phone' => '+481234567890',
        ]);
        $response->assertJsonStructure(['token', 'user']);
        $this->assertNotEmpty($response->json('token'));
    }
}
