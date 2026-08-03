<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requires_authentication(): void
    {
        User::factory()->create();

        $this->getJson('/api/users')
            ->assertUnauthorized();
    }

    public function test_it_rejects_invalid_credentials(): void
    {
        $admin = User::factory()->admin()->create();

        $this->withBasicAuth($admin->email, 'wrong-password')
            ->getJson('/api/users')
            ->assertUnauthorized();
    }

    public function test_it_returns_only_active_users(): void
    {
        $active = User::factory()->create(['name' => 'Active User']);
        User::factory()->create(['name' => 'Inactive User', 'active' => false]);

        $this->authAs(User::factory()->admin()->create())
            ->getJson('/api/users')
            ->assertOk()
            ->assertJsonPath('users.0.id', $active->id)
            ->assertJsonMissing(['name' => 'Inactive User']);
    }

    public function test_it_excludes_the_password_field(): void
    {
        $this->authAs(User::factory()->admin()->create())
            ->getJson('/api/users')
            ->assertOk()
            ->assertJsonMissing(['password']);
    }

    public function test_it_includes_the_orders_count(): void
    {
        $user = User::factory()->create();
        Order::factory()->count(3)->create(['user_id' => $user->id]);

        $this->authAs(User::factory()->admin()->create())
            ->getJson('/api/users')
            ->assertOk()
            ->assertJsonPath('users.0.orders_count', 3);
    }

    public function test_it_filters_by_search_on_name_or_email(): void
    {
        User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);

        $this->authAs(User::factory()->admin()->create())
            ->getJson('/api/users?search=john')
            ->assertOk()
            ->assertJsonCount(1, 'users')
            ->assertJsonPath('users.0.email', 'john@example.com');

        $this->authAs(User::factory()->admin()->create())
            ->getJson('/api/users?search=jane@example.com')
            ->assertOk()
            ->assertJsonCount(1, 'users')
            ->assertJsonPath('users.0.name', 'Jane Smith');
    }

    public function test_it_sorts_by_name_email_and_created_at(): void
    {
        User::factory()->create(['name' => 'Charlie']);
        User::factory()->create(['name' => 'Alice']);
        User::factory()->create(['name' => 'Bob']);

        $this->authAs(User::factory()->admin()->create(['name' => 'Zed Admin']))
            ->getJson('/api/users?sortBy=name')
            ->assertOk()
            ->assertJsonPath('users.0.name', 'Alice')
            ->assertJsonPath('users.1.name', 'Bob')
            ->assertJsonPath('users.2.name', 'Charlie');
    }

    public function test_it_defaults_to_sorting_by_created_at_descending(): void
    {
        $first = User::factory()->create(['created_at' => now()->subDay()]);
        $second = User::factory()->create(['created_at' => now()]);

        $this->authAs(User::factory()->admin()->create(['created_at' => now()->subDays(2)]))
            ->getJson('/api/users')
            ->assertOk()
            ->assertJsonPath('users.0.id', $second->id)
            ->assertJsonPath('users.1.id', $first->id);
    }

    public function test_it_paginates_results(): void
    {
        User::factory()->count(15)->create();

        $this->authAs(User::factory()->admin()->create())
            ->getJson('/api/users?page=2')
            ->assertOk()
            ->assertJsonPath('page', 2)
            ->assertJsonCount(6, 'users');
    }

    public function test_admin_can_edit_any_user(): void
    {
        $admin = User::factory()->admin()->create();
        $manager = User::factory()->manager()->create();
        $user = User::factory()->create();

        $this->authAs($admin)
            ->getJson('/api/users')
            ->assertOk()
            ->assertJsonPath('users.0.can_edit', true)
            ->assertJsonPath('users.1.can_edit', true)
            ->assertJsonPath('users.2.can_edit', true);
    }

    public function test_manager_can_only_edit_users_with_role_user(): void
    {
        $manager = User::factory()->manager()->create();
        $otherManager = User::factory()->manager()->create();
        $user = User::factory()->create();

        $this->authAs($manager)
            ->getJson('/api/users')
            ->assertOk()
            ->assertJsonPath('users.0.can_edit', false) // the manager themselves
            ->assertJsonPath('users.1.can_edit', false) // the other manager
            ->assertJsonPath('users.2.can_edit', true);  // the regular user
    }

    public function test_user_can_only_edit_themselves(): void
    {
        $currentUser = User::factory()->create(['name' => 'Current User']);
        $other = User::factory()->create(['name' => 'Other User']);

        $this->authAs($currentUser)
            ->getJson('/api/users')
            ->assertOk()
            ->assertJsonPath('users.0.can_edit', true)
            ->assertJsonPath('users.1.can_edit', false);
    }

    public function test_it_validates_the_request_input(): void
    {
        $this->authAs(User::factory()->admin()->create())
            ->getJson('/api/users?page=0&sortBy=invalid')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['page', 'sortBy']);
    }

    /**
     * Authenticate a request using HTTP Basic auth with the given user.
     */
    private function authAs(User $user): static
    {
        return $this->withBasicAuth($user->email, 'password');
    }
}
