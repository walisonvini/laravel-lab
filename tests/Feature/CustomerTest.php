<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_customer_with_its_user(): void
    {
        $response = $this->postJson('/api/customers', $this->validPayload());

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('result.document', '12345678901')
            ->assertJsonPath('result.user.email', 'joao.silva@example.com')
            ->assertJsonMissingPath('result.user.password');

        $user = User::where('email', 'joao.silva@example.com')->firstOrFail();

        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertDatabaseHas('customers', [
            'user_id' => $user->id,
            'document' => '12345678901',
            'phone' => '11999999999',
        ]);
    }

    public function test_it_requires_the_mandatory_fields(): void
    {
        $response = $this->postJson('/api/customers', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password', 'document']);

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('customers', 0);
    }

    public function test_it_rejects_an_email_or_document_already_in_use(): void
    {
        $existing = Customer::factory()->create();

        $response = $this->postJson('/api/customers', $this->validPayload([
            'email' => $existing->user->email,
            'document' => $existing->document,
        ]));

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'document']);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('customers', 1);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'João Silva',
            'email' => 'joao.silva@example.com',
            'password' => 'password123',
            'document' => '12345678901',
            'phone' => '11999999999',
            'birth_date' => '1995-05-20',
        ], $overrides);
    }
}
