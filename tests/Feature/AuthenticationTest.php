<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_new_users_can_register():void
    {
        $response = $this->post('/api/auth/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'role' => 'Vendor',
            'phone_number' => '08123456789',
            'email' => 'test@example.com',
            'password' => 'password'
        ]);
        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen()
    {
        $response = $this->post('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        $this->assertAuthenticated();
        $response->assertStatus(200);
    }

    public function test_vendor_link()
    {
        $response = $this->get('/api/qr/grand-towers-hotel');
        $response->assertStatus(200);
    }
}
