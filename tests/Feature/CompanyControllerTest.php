<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;

   public function setUp(): void
    {
        parent::setUp();
        $this->authenticate();
    }

    public function test_index_returns_companies()
    {
        Company::factory()->count(2)->create();

        $response = $this->getJson(route('api.companies.index'));

        $response->assertOk()
            ->assertJsonCount(2);
    }

    public function test_store_creates_company()
    {
        $data = [
            'name' => 'Test Company',
            'tax_id_number' => '123456789',
            'address' => '123 Main St',
            'city' => 'Testville',
            'postal_code' => '12345',
        ];

        $response = $this->postJson(route('api.companies.store'), $data);

        $response->assertCreated()
            ->assertJsonFragment(['name' => 'Test Company']);
        $this->assertDatabaseHas('companies', ['name' => 'Test Company']);
    }

    public function test_show_returns_company()
    {
        $company = Company::factory()->create();

        $response = $this->getJson(route('api.companies.show', $company));

        $response->assertOk()
            ->assertJsonFragment(['id' => $company->id]);
    }

    public function test_update_modifies_company()
    {
        $company = Company::factory()->create();
        $data = ['name' => 'Updated Name',
                 'tax_id_number' => '987654321',
                 'address' => '456 Another St',
                 'city' => 'New City',
                 'postal_code' => '67890'];

        $response = $this->putJson(route('api.companies.update', $company), $data);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Updated Name']);
        $this->assertDatabaseHas('companies', ['id' => $company->id, 'name' => 'Updated Name']);
    }

    public function test_destroy_deletes_company()
    {
        $company = Company::factory()->create();

        $response = $this->deleteJson(route('api.companies.destroy', $company));

        $response->assertOk()
            ->assertJsonFragment(['message' => 'Company deleted successfully']);
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }

    public function test_store_requires_required_fields()
    {
        $response = $this->postJson(route('api.companies.store'), []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'tax_id_number', 'address', 'city', 'postal_code']);
    }

    public function test_store_requires_unique_tax_id_number()
    {
        Company::factory()->create(['tax_id_number' => 'DUPLICATE']);
        $data = [
            'name' => 'Another',
            'tax_id_number' => 'DUPLICATE',
            'address' => 'Somewhere',
            'city' => 'City',
            'postal_code' => '00000',
        ];
        $response = $this->postJson(route('api.companies.store'), $data);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tax_id_number']);
    }

    public function test_update_requires_unique_tax_id_number()
    {
        $company1 = Company::factory()->create(['tax_id_number' => 'UNIQUE1']);
        $company2 = Company::factory()->create(['tax_id_number' => 'UNIQUE2']);

        $data = ['tax_id_number' => 'UNIQUE1',
                 'name' => 'Updated Company',
                 'address' => '456 Another St',
                 'city' => 'New City',
                 'postal_code' => '67890'];
        $response = $this->putJson(route('api.companies.update', $company2), $data);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tax_id_number']);
    }

    public function test_update_allows_same_tax_id_number_for_same_company()
    {
        $company = Company::factory()->create(['tax_id_number' => 'SAME123']);
        $data = ['tax_id_number' => 'SAME123',
                 'name' => 'Updated Company',
                 'address' => '456 Another St',
                 'city' => 'New City',
                 'postal_code' => '67890'];
        $response = $this->putJson(route('api.companies.update', $company), $data);
        $response->assertStatus(200)
            ->assertJsonFragment(['tax_id_number' => 'SAME123']);
    }

     protected function authenticate()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        return $user;
    }
}
