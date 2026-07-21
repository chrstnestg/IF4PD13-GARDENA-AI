<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_register()
    {
        $response = $this->post('/register', [
            'nama' => 'Irene',
            'username' => 'irene123',
            'email' => 'irene@test.com',
            'password' => 'Password@123',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('users', [
            'email' => 'irene@test.com',
        ]);
    }

    #[Test]
    public function user_can_login()
    {
        User::create([
            'name' => 'Irene',
            'username' => 'irene123',
            'email' => 'irene@test.com',
            'password' => bcrypt('Password@123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'irene@test.com',
            'password' => 'Password@123',
        ]);

        $response->assertRedirect('/login');

        $this->assertAuthenticated();
    }
}