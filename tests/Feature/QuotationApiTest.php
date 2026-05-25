<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotationApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        config(['jwt.secret' => 'quotation-feature-test-secret-32-bytes']);
        $this->user = User::factory()->create();
    }

    public function test_an_authorized_user_can_create_a_quotation_using_inclusive_trip_days(): void
    {
        $response = $this
            ->withToken($this->tokenFor($this->user))
            ->postJson('/quotation', [
                'age' => '28,35',
                'currency_id' => 'EUR',
                'start_date' => '2020-10-01',
                'end_date' => '2020-10-30',
            ]);

        $response
            ->assertCreated()
            ->assertExactJson([
                'total' => 117.00,
                'currency_id' => 'EUR',
                'quotation_id' => 1,
            ]);

        $this->assertDatabaseHas('quotations', [
            'id' => 1,
            'user_id' => $this->user->id,
            'currency_id' => 'EUR',
            'trip_length' => 30,
            'total' => '117.00',
        ]);
    }

    public function test_all_supported_age_boundaries_are_calculated(): void
    {
        $this
            ->withToken($this->tokenFor($this->user))
            ->postJson('/quotation', [
                'age' => '18,30,31,40,41,50,51,60,61,70',
                'currency_id' => 'USD',
                'start_date' => '2026-06-01',
                'end_date' => '2026-06-01',
            ])
            ->assertCreated()
            ->assertJsonPath('total', 24);
    }

    public function test_a_bearer_token_is_required(): void
    {
        $this->postJson('/quotation', $this->validPayload())
            ->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_the_api_requires_a_json_request_body(): void
    {
        $this
            ->withToken($this->tokenFor($this->user))
            ->post('/quotation', $this->validPayload())
            ->assertStatus(415)
            ->assertJson(['message' => 'Requests must use application/json content.']);
    }

    public function test_invalid_quotation_input_returns_validation_errors(): void
    {
        $this
            ->withToken($this->tokenFor($this->user))
            ->postJson('/quotation', [
                'age' => '17,71,not-an-age',
                'currency_id' => 'CAD',
                'start_date' => '2026-06-03',
                'end_date' => '2026-06-01',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['age', 'currency_id', 'end_date']);
    }

    private function tokenFor(User $user): string
    {
        return $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->json('token');
    }

    private function validPayload(): array
    {
        return [
            'age' => '28',
            'currency_id' => 'GBP',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-05',
        ];
    }
}
