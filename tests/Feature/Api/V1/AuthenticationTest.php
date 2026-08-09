<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_successfully()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'createdAt',
                    ],
                    'token',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        // Assert that password is hashed and not stored as plain text
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotEquals('password', $user->password);
        $this->assertTrue(password_verify('password', $user->password));

        // Assert token is returned
        $response->assertJsonFragment([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $responseData = $response->json('data');
        $this->assertNotEmpty($responseData['token']);
        $this->assertIsString($responseData['token']);
    }

    public function test_register_returns_validation_errors_for_missing_fields()
    {
        $response = $this->postJson('/api/v1/auth/register', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_register_returns_validation_error_for_password_mismatch()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'different',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }

    public function test_register_returns_validation_error_for_short_password()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }

    public function test_register_returns_validation_error_for_duplicate_email()
    {
        User::factory()->create([
            'email' => 'john@example.com',
        ]);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_register_normalizes_email_to_lowercase()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'JOHN@EXAMPLE.COM',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_register_trims_name()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => '  John Doe  ',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
        ]);
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'createdAt',
                    ],
                    'token',
                ],
            ]);

        $response->assertJsonFragment([
            'name' => $user->name,
            'email' => 'john@example.com',
        ]);

        $responseData = $response->json('data');
        $this->assertNotEmpty($responseData['token']);
        $this->assertIsString($responseData['token']);
    }

    public function test_login_returns_error_with_incorrect_password()
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'john@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertUnauthorized()
            ->assertJson([
                'message' => 'The provided credentials are incorrect.',
            ]);
    }

    public function test_login_returns_same_error_for_nonexistent_email()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $response->assertUnauthorized()
            ->assertJson([
                'message' => 'The provided credentials are incorrect.',
            ]);
    }

    public function test_login_normalizes_email_to_lowercase()
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'JOHN@EXAMPLE.COM',
            'password' => 'password',
        ]);

        $response->assertOk();
    }

    public function test_authenticated_user_can_access_me_endpoint()
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/me');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'createdAt',
                ],
            ])
            ->assertJsonFragment([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    public function test_me_endpoint_returns_unauthorized_without_token()
    {
        $response = $this->getJson('/api/v1/me');

        $response->assertUnauthorized();
    }

    public function test_me_endpoint_returns_unauthorized_with_invalid_token()
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
            ->getJson('/api/v1/me');

        $response->assertUnauthorized();
    }

    public function test_logout_deletes_only_the_current_token()
    {
        $user = User::factory()->create();

        // Create two tokens
        $tokenResult1 = $user->createToken('test-token-1');
        $token1 = $tokenResult1->plainTextToken;

        $tokenResult2 = $user->createToken('test-token-2');
        $token2 = $tokenResult2->plainTextToken;

        // Verify both tokens exist
        $this->assertEquals(2, $user->tokens()->count());

        // Logout using the first token
        $response = $this->withHeader('Authorization', 'Bearer '.$token1)
            ->postJson('/api/v1/auth/logout');

        $response->assertNoContent();

        // Verify only the first token was deleted
        $this->assertEquals(1, $user->fresh()->tokens()->count());

        // Verify the second token still works
        $response = $this->withHeader('Authorization', 'Bearer '.$token2)
            ->getJson('/api/v1/me');

        $response->assertOk();
    }

    public function test_deleted_token_is_rejected()
    {
        $user = User::factory()->create();
        $tokenResult = $user->createToken('test-token');
        $token = $tokenResult->plainTextToken;

        // Delete the token directly from the database
        $tokenResult->accessToken->delete();

        // Try to use the deleted token
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/me');

        $response->assertUnauthorized();
    }

    public function test_another_token_remains_valid_after_first_is_deleted()
    {
        $user = User::factory()->create();

        // Create two tokens
        $tokenResult1 = $user->createToken('test-token-1');
        $token1 = $tokenResult1->plainTextToken;

        $tokenResult2 = $user->createToken('test-token-2');
        $token2 = $tokenResult2->plainTextToken;

        // Delete the first token
        $tokenResult1->accessToken->delete();

        // The second token should still work
        $response = $this->withHeader('Authorization', 'Bearer '.$token2)
            ->getJson('/api/v1/me');

        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function test_logout_end_to_end()
    {
        // Register a user
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertCreated();
        $token = $response->json('data.token');

        // Verify the token works
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/me');

        $response->assertOk();

        // Logout
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/auth/logout');

        $response->assertNoContent();

        // Manually forget the user resolution to simulate a fresh request
        Auth::forgetGuards();

        // Verify the token no longer works
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/me');

        $response->assertUnauthorized();
    }

    public function test_register_endpoint_is_rate_limited()
    {
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/v1/auth/register', [
                'name' => 'John Doe '.$i,
                'email' => 'john'.$i.'@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);
        }

        $response->assertStatus(429);
    }

    public function test_login_endpoint_is_rate_limited()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/v1/auth/login', [
                'email' => 'john@example.com',
                'password' => 'wrong-password',
            ]);
        }

        $response->assertStatus(429);
    }

    public function test_health_endpoint_still_works()
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'status' => 'ok',
                    'application' => 'Parea API',
                ],
            ]);
    }

    public function test_unknown_api_routes_still_return_safe_json_404()
    {
        $response = $this->getJson('/api/v1/nonexistent');

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Not found.',
            ])
            ->assertHeader('content-type', 'application/json');
    }
}
