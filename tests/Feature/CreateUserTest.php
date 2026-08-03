<?php

namespace Tests\Feature;

use App\Mail\NewUserNotificationEmail;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_user_and_returns_its_details(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'password' => 'secret123',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'role' => 'user',
            'active' => true,
        ]);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'email',
                'name',
                'created_at',
            ],
        ]);

        $response->assertJsonPath('data.email', 'john@example.com');
        $response->assertJsonPath('data.name', 'John Doe');
        $response->assertJsonMissing(['password']);

        $user = User::where('email', 'john@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_it_sends_a_welcome_email_to_the_new_user(): void
    {
        Mail::fake();

        $this->postJson('/api/users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'password' => 'secret123',
        ]);

        Mail::assertQueued(WelcomeEmail::class, function (WelcomeEmail $mail) {
            return $mail->hasTo('john@example.com')
                && $mail->user->email === 'john@example.com';
        });
    }

    public function test_it_notifies_all_admin_users_about_the_new_user(): void
    {
        Mail::fake();

        $adminOne = User::factory()->admin()->create(['email' => 'admin1@example.com']);
        $adminTwo = User::factory()->admin()->create(['email' => 'admin2@example.com']);

        $this->postJson('/api/users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'password' => 'secret123',
        ]);

        Mail::assertQueued(NewUserNotificationEmail::class, function (NewUserNotificationEmail $mail) use ($adminOne, $adminTwo) {
            return $mail->hasTo($adminOne->email)
                && $mail->hasTo($adminTwo->email)
                && $mail->user->email === 'john@example.com';
        });
    }

    public function test_it_validates_the_request_input(): void
    {
        $this->postJson('/api/users', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password', 'name']);

        $this->postJson('/api/users', [
            'email' => 'not-an-email',
            'name' => 'Jo',
            'password' => 'short',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'name', 'password']);
    }

    public function test_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->postJson('/api/users', [
            'email' => 'taken@example.com',
            'name' => 'John Doe',
            'password' => 'secret123',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }
}
